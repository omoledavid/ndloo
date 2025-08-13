<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UploadImageRequest;
use App\Models\ProfileImage;
use App\Models\User;
use App\Support\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use App\Services\VisionService;
class ProfileController extends Controller
{
    protected $vision;
    public function __construct(private readonly ProfileService $profileService, VisionService $vision)
    {
        $this->vision = $vision;
    }

    public function profile(User $user): JsonResponse
    {
        return $this->profileService->profile($user);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        return $this->profileService->updateProfile($request);
    }

    public function uploadImages(UploadImageRequest $request): JsonResponse
    {
        return $this->profileService->uploadImages($request);
    }

    public function removeImage(ProfileImage $image): JsonResponse
    {
        return $this->profileService->removeImage($image);
    }
    public function viewAuthUser()
    {
        $user =  auth()->user();

        return $user;
    }
}
