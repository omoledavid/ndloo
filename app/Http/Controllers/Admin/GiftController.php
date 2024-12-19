<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftPlan;
use App\Models\UserGift;
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
            return $this->successResponse(message: 'Gift created successfully', data: [
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

    public function giftStats()
    {
        // Total revenue calculation
        $totalGiftsRevenue = UserGift::with('plan')->get()->sum(function ($gift) {
            return $gift->plan->amount;
        });

        // Total gifts and purchases
        $totalGiftsPurchased = UserGift::count();
        $totalGiftPlans = GiftPlan::count();

        // Weekly sales data
        $weeklySales = UserGift::selectRaw('YEAR(user_gifts.created_at) as year, WEEK(user_gifts.created_at) as week, SUM(plan.amount) as total_sales')
            ->join('gift_plans as plan', 'user_gifts.gift_plan_id', '=', 'plan.id')
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        // Monthly sales data
        $monthlySales = UserGift::selectRaw('YEAR(user_gifts.created_at) as year, MONTH(user_gifts.created_at) as month, SUM(plan.amount) as total_sales')
            ->join('gift_plans as plan', 'user_gifts.gift_plan_id', '=', 'plan.id')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format weekly sales data for chart
        $weeklySalesData = $weeklySales->map(function ($sale) {
            return [
                'label' => "Week {$sale->week}, {$sale->year}",
                'value' => $sale->total_sales,
            ];
        });

        // Format monthly sales data for chart
        $monthlySalesData = $monthlySales->map(function ($sale) {
            return [
                'label' => date('F Y', mktime(0, 0, 0, $sale->month, 1, $sale->year)), // Convert month to name
                'value' => $sale->total_sales,
            ];
        });

        // Return the data
        return $this->successResponse(data: [
            'general_stats' => [
                'total_gifts' => $totalGiftPlans,
                'total_gifts_purchased' => $totalGiftsPurchased,
                'total_gift_revenue' => $totalGiftsRevenue . ' USD',
            ],
            'total_gift_sales' => [
                'byWeek' => $weeklySalesData,
                'byMonth' => $monthlySalesData,
            ],
        ]);
    }


}
