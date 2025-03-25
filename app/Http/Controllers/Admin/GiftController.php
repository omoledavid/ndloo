<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Enums\GiftStatus;
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

        $icon = env('APP_URL') . "/public/storage/" . $uploaded;

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
            $icon = env('APP_URL') . "/public/storage/" . $uploaded;
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

    public function statusToggle(GiftPlan $gift)
    {
        $giftStatus = $gift->status == GiftStatus::DISABLE->value ? GiftStatus::ENABLE->value : GiftStatus::DISABLE->value;

        $gift->update([
            'status' => $giftStatus
        ]);

        return $this->successResponse('Gift status updated successfully');
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
        // Get weekly sales data
        $weeklyPurchase = UserGift::selectRaw("
                YEAR(user_gifts.created_at) as year,
                WEEK(user_gifts.created_at, 1) as week,
                DAYOFWEEK(user_gifts.created_at) as day_number,
                DAYNAME(user_gifts.created_at) as day,
                COALESCE(SUM(plan.amount), 0) as total_sales
            ")
            ->join('gift_plans as plan', 'user_gifts.gift_plan_id', '=', 'plan.id')
            ->whereRaw("YEARWEEK(user_gifts.created_at, 1) = YEARWEEK(CURDATE(), 1)") // Filter for current week
            ->groupBy('year', 'week', 'day_number', 'day')
            ->orderBy('day_number', 'asc')
            ->get();

        // Get monthly sales data
        $monthlyPurchase = UserGift::selectRaw("
                YEAR(user_gifts.created_at) as year,
                MONTH(user_gifts.created_at) as month,
                COALESCE(SUM(plan.amount), 0) as total_sales
            ")
                    ->join('gift_plans as plan', 'user_gifts.gift_plan_id', '=', 'plan.id')
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'asc')
                    ->orderBy('month', 'asc')
                    ->get();
        $weeklySales = UserGift::where('status', 'redeemed')->selectRaw("
                YEAR(user_gifts.created_at) as year,
                WEEK(user_gifts.created_at, 1) as week,
                DAYOFWEEK(user_gifts.created_at) as day_number,
                DAYNAME(user_gifts.created_at) as day,
                COALESCE(SUM(plan.amount), 0) as total_sales
            ")
            ->join('gift_plans as plan', 'user_gifts.gift_plan_id', '=', 'plan.id')
            ->whereRaw("YEARWEEK(user_gifts.created_at, 1) = YEARWEEK(CURDATE(), 1)") // Filter for current week
            ->groupBy('year', 'week', 'day_number', 'day')
            ->orderBy('day_number', 'asc')
            ->get();

        // Get monthly sales data
        $monthlySales = UserGift::where('status', 'redeemed')->selectRaw("
                YEAR(user_gifts.created_at) as year,
                MONTH(user_gifts.created_at) as month,
                COALESCE(SUM(plan.amount), 0) as total_sales
            ")
                    ->join('gift_plans as plan', 'user_gifts.gift_plan_id', '=', 'plan.id')
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'asc')
                    ->orderBy('month', 'asc')
                    ->get();

        // Reference arrays for all days and months
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // Initialize arrays with zero values
        $weeklyPurchaseData = [];
        foreach ($weekDays as $day) {
            $weeklyPurchaseData[$day] = [
                'label' => $day,
                'value' => 0
            ];
        }

        // Populate with actual sales data
        foreach ($weeklyPurchase as $sale) {
            $weeklyPurchaseData[$sale->day]['value'] = $sale->total_sales;
        }

        // Convert back to indexed array
        $weeklyPurchaseData = array_values($weeklyPurchaseData);

        // Initialize arrays with zero values for months
        $monthlyPurchaseData = [];
        $monthlySales = [];
        foreach ($months as $num => $name) {
            $monthlyPurchaseData[$num] = [
                'label' => "$name",
                'value' => 0
            ];
            $monthlySales[$num] = [
                'label' => "$name",
                'value' => 0
            ];
        }

        // Populate with actual sales data
        foreach ($monthlyPurchase as $sale) {
            $monthlyPurchaseData[$sale->month]['value'] = $sale->total_sales;
        }
        foreach ($monthlySales as $sale) {
            $monthlySalesData[$sale->month]['value'] = $sale->total_sales;
        }

        // Convert back to indexed array
        $monthlyPurchaseData = array_values($monthlyPurchaseData);
        $monthlySalesData = array_values($monthlySalesData);


        // Return the data
        return $this->successResponse(data: [
            'general_stats' => [
                'total_gifts' => $totalGiftPlans,
                'gift_profit' => 0,
                'total_gifts_purchased' => $totalGiftsPurchased,
                'total_gift_revenue' => $totalGiftsRevenue . ' USD',
            ],
            'total_gift_purchase' => [
                'byWeek' => $weeklyPurchaseData,
                'byMonth' => $monthlyPurchaseData,
            ],
            'total_gift_sales' => [
                'byWeek' => [],
                'byMonth' => $monthlySalesData
            ]
        ]);
    }


}
