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

        $uploadedFile = FileUpload::uploadFile($request->file('images'), folder: 'images');

        ProfileImage::create([
            'user_id' => $request->user()->id,
            'image' => env('APP_URL').'/storage/'.$uploadedFile,
        ]);

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
