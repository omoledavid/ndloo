<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostPlan;
use App\Models\BoostPlanUser;
use App\Support\Services\BaseService;
use Illuminate\Http\Request;

class BoostController extends BaseService
{
    public function boost()
    {
        return $this->successResponse(data: [
            'boost_plans' => BoostPlan::all(),
        ]);
    }

    public function createBoost(Request $request)
    {
        $request->validate([
            'period' => 'required|int',
            'price' => 'required|int',
        ]);
        if ($boost = BoostPlan::create([
            'period' => $request->period,
            'price' => $request->price,
        ])) {
            return $this->successResponse(message: 'Boost plan created successfully', data: [
                'boost' => $boost,
            ]);
        }
        return $this->errorResponse('Failed to create boost');
    }

    public function updateBoost(BoostPlan $boost, Request $request)
    {
        $request->validate([
            'period' => 'required|int',
            'price' => 'required|int',
        ]);

        $boost = BoostPlan::find($boost->id);

        if ($boost) {
            // Update the boost plan
            $boost->update([
                'period' => $request->period,
                'price' => $request->price,
            ]);

            // Return the updated boost plan
            return $this->successResponse(message: 'Boost plan updated', data: [
                'boost' => $boost,  // The updated boost plan is now here
            ]);
        } else {
            // If boost plan not found, return an error
            return $this->errorResponse(message: 'Boost plan not found');
        }
    }
    public function boostStats()
    {
        return $this->successResponse(data: [
            'total_boost_plans' => BoostPlan::all()->count(),
            'total_activated_boost' => BoostPlanUser::all()->count(),
            'total_active_boost' => BoostPlanUser::where('active', true)->count(),
        ]);
    }

    public function viewBoostPlan(BoostPlan $boostPlan)
    {
        return $this->successResponse(data: [
            'boost_plan' => $boostPlan,
        ]);
    }
}
