<?php

namespace App\Services;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Image;

class VisionService
{
    protected $client;

    public function __construct()
    {
        $this->client = new ImageAnnotatorClient([
            'credentials' => base_path(env('GOOGLE_APPLICATION_CREDENTIALS'))
        ]);
    }

    public function detectAdultContent(string $imagePath)
    {
        $content = file_get_contents($imagePath);
        $image = (new Image())->setContent($content);

        $feature = (new Feature())->setType(Feature\Type::SAFE_SEARCH_DETECTION);

        $request = (new AnnotateImageRequest())
            ->setImage($image)
            ->setFeatures([$feature]);

        $batch = (new BatchAnnotateImagesRequest())
            ->setRequests([$request]);

        $response = $this->client->batchAnnotateImages($batch)
                   ->getResponses()[0];

        $safe = $response->getSafeSearchAnnotation();

        return [
            'adult'    => $safe->getAdult(),
            'violence' => $safe->getViolence(),
            'racy'     => $safe->getRacy(),
            'medical'  => $safe->getMedical(),
        ];
    }

    public function detectFaces(string $imagePath)
    {
        $content = file_get_contents($imagePath);
        $image = (new Image())->setContent($content);

        $feature = (new Feature())->setType(Feature\Type::FACE_DETECTION);

        $request = (new AnnotateImageRequest())
            ->setImage($image)
            ->setFeatures([$feature]);

        $batch = (new BatchAnnotateImagesRequest())
            ->setRequests([$request]);

        $response = $this->client->batchAnnotateImages($batch)
                   ->getResponses()[0];

        return count($response->getFaceAnnotations());
    }
}
