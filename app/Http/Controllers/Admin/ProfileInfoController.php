<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileInfo;
use App\Support\Services\BaseService;
use Illuminate\Http\Request;

class ProfileInfoController extends BaseService
{
    public function profileInfo()
    {
        return $this->successResponse(data:[
            'options' => ProfileInfo::query()->get()
        ]);
    }
    public function saveProfileInfo(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'type' => 'required|in:select,input',
            'options' => 'nullable|array',
        ]);
        ProfileInfo::query()->updateOrCreate($validatedData);
        return $this->successResponse('Profile info Created Successfully');

    }
    public function updateProfileInfo(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:profile_infos,id',
            'name' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'type' => 'required|in:select,input',
            'options' => 'nullable|array',
        ]);
        $profileinfo = ProfileInfo::query()->find($request->id)->update($validatedData);
        return $this->successResponse('Profile info Created Successfully');

    }
}
