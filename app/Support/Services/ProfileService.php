<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\ProfileImage;
use App\Models\ProfileInfo;
use App\Models\User;
use App\Services\VisionService;
use App\Support\Helpers\FileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileService extends BaseService
{
    protected $vision;
    public function __construct(VisionService $vision)
    {
        $this->vision = $vision;
    }
    public function profile(User $user): JsonResponse
    {
        $user->load('profile', 'images');
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
        $uploadedImages = [];
        $firstImagePath = null;

        foreach ($request->file('images') as $index => $image) {
            // Upload the image
            $uploadedFile = FileUpload::uploadFile($image, folder: 'images');

            // Build local path and public URL
            $localPath = storage_path('app/public/' . $uploadedFile);
            $publicUrl = env('APP_URL') . '/public/storage/' . $uploadedFile;

            // Nudity check
            $safeCheck = $this->vision->detectAdultContent($localPath);
            if (in_array($safeCheck['adult'], [3, 4, 5]) || in_array($safeCheck['racy'], [3, 4, 5])) {
                // 0 = UNKNOWN
                // 1 = VERY_UNLIKELY
                // 2 = UNLIKELY
                // 3 = POSSIBLE
                // 4 = LIKELY
                // 5 = VERY_LIKELY
                try {
                    Storage::disk('public')->delete($uploadedFile);
                } catch (\Throwable $th) {
                    Log::error($th);
                }
                return $this->errorResponse(__('responses.nudityDetected'));
            }

            // Face detection
            $faceCount = $this->vision->detectFaces($localPath);
            if ($faceCount !== 1) {
                Storage::disk('public')->delete($uploadedFile);
                return $this->errorResponse(__('responses.faceDetectionFailed'));
            }

            // Save to DB
            ProfileImage::create([
                'user_id' => $request->user()->id,
                'image' => $publicUrl,
            ]);

            // Set first image as avatar
            if ($index === 0) {
                $firstImagePath = $publicUrl;
            }

            $uploadedImages[] = $publicUrl;
        }

        // Update user's avatar
        if ($firstImagePath) {
            $request->user()->update(['avatar' => $firstImagePath]);
        }

        return $this->successResponse(__('responses.imagesUploaded'), ['images' => $uploadedImages]);
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
