<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\ProfileImage;
use App\Models\ProfileInfo;
use App\Models\User;
use App\Support\Helpers\FileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileService extends BaseService
{
    public function profile(User $user): JsonResponse
    {
        $user->load('profile','images');
        $profileInfo = ProfileInfo::all()->groupBy('category');
        $profile = [];

        foreach ($user->profile as $prof) {
            $profile[$prof->pivot->profile_info_id] = $prof->pivot->content;
        }

        return $this->successResponse(data: [
            'info' => $profileInfo,
            'profile' => collect($profile),
            'person' => $user,
        ]);
    }

    public function updateProfile(object $request): JsonResponse
    {
        $request->user()->profile()->detach(array_keys($request->details));

        foreach ($request->details as $id => $content) {
            $request->user()->profile()->attach($id, ['content' => $content]);
        }

        return $this->successResponse(__('responses.profileUpdated'));
    }

    public function uploadImages(object $request): JsonResponse
    {
        $uploadedImages = []; // Store the uploaded image paths
        $firstImagePath = null; // To store the first uploaded image

        foreach ($request->file('images') as $index => $image) {
            // Upload each image and get the file path
            $uploadedFile = FileUpload::uploadFile($image, folder: 'images');
            $imagePath = env('APP_URL').'/public/storage/'.$uploadedFile;

            // Save the image information to the database
            ProfileImage::create([
                'user_id' => $request->user()->id,
                'image' => $imagePath,
            ]);

            // Store the first image path
            if ($index === 0) {
                $firstImagePath = $imagePath;
            }

            // Add the uploaded file path to the response
            $uploadedImages[] = $imagePath;
        }

        // Update user's avatar with the first uploaded image
        if ($firstImagePath) {
            $request->user()->update(['avatar' => $firstImagePath]);
        }

        return $this->successResponse(__('responses.imagesUploaded'));
    }


    public function removeImage(ProfileImage $profileImage): JsonResponse
    {

        $profileImage->delete();
        try {
            $path = explode('storage/', $profileImage->image)[1];
            Log::info($path);
            Storage::disk('public')->delete($path);
        } catch (\Throwable $th) {
            Log::error($th);
        }

        return $this->successResponse(__('responses.imageRemoved'));
    }
}
