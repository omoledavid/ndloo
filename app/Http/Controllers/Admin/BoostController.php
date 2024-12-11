<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostPlan;
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
        return $this->successResponse(message: 'wokring fine');
    }
    public function viewBoostPlan(BoostPlan $boostPlan)
    {
        return $this->successResponse(data: [
            'boost_plan' => $boostPlan,
        ]);
    }
}
