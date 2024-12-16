<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftPlan;
use App\Support\Services\BaseService;
use App\Support\Services\FileUploadService;
use Illuminate\Http\Request;

class GiftController extends BaseService
{
    public function getGifts()
    {
        return $this->successResponse(data: [
            'gifts' => GiftPlan::all(),
        ]);
    }
    public function viewGifts($id)
    {
        return $this->successResponse(data: [
            'gift' => GiftPlan::where('id', $id)->first(),
        ]);
    }
    public function createGift(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric|min:0',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $uploaded = FileUploadService::uploadFile($request->file('icon'), 'gifts');

        if (!$uploaded) {
            return $this->errorResponse(message: 'Error uploading file');
        }

        $icon = env('APP_URL') . "/storage/" . $uploaded;

        if ($gift = GiftPlan::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'icon' => $icon
        ])) {
            return $this->successResponse(message: 'Gift created successfully',data: [
                'gift' => $gift,
            ]);
        }

        return $this->errorResponse(message: 'Error creating gift');
    }
    public function editGift(GiftPlan $gift, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric|min:0',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $uploaded = "pending";

        if ($request->file('icon')) {
            $uploaded = FileUploadService::uploadFile($request->file('icon'), 'gifts');
            $icon = env('APP_URL') . "/storage/" . $uploaded;
        }

        if (!$uploaded) {
            return $this->errorResponse(message: 'Error uploading file');
        }

        if ($gift->update([
            'name' => $request->name ?? $gift->name,
            'amount' => $request->amount ?? $gift->amount,
            'icon' => $request->file('icon') ? $icon : $gift->icon
        ])) {
            return $this->successResponse(
                message: 'Gift updated successfully',
                data: [
                    'gift' => $gift,
                ]
            );
        }

        return redirect()->back()->withErrors(['error' => 'Error updating gift']);
    }
}
