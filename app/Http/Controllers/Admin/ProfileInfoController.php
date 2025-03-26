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
            'All' => ProfileInfo::query()->get(),
            'General' => ProfileInfo::query()->where('category', 'General')->get(),
            'Appearance' => ProfileInfo::query()->where('category', 'Appearance')->get(),
            'Personality' => ProfileInfo::query()->where('category','Personality', )->get(),
            'Lifestyle' => ProfileInfo::query()->where('category','Lifestyle', )->get(),
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
