<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Enums\UserStates;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageUsersController extends BaseService
{
    public function allUsers(Request $request)
    {
        // Start the query to get all users
        $query = User::query();

        // Filter by name if provided
        if ($request->has('name') && $request->name != '') {
            $query->where(function ($query) use ($request) {
                $query->where('firstname', 'like', '%' . $request->name . '%')
                    ->orWhere('lastname', 'like', '%' . $request->name . '%');
            });
        }


        // Filter by email if provided
        if ($request->has('email') && $request->email != '') {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by location if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by year if provided (ensure 'created_at' is used or other date field)
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('created_at', $request->year);
        }

        // Get the filtered users
        $allUsers = $query->get();

        // Return the filtered users
        return $this->successResponse(data: [
            'all_users' => $allUsers,
        ]);
    }


    public function viewUser(User $user)
    {
        return $this->successResponse(data: [
            'user' => $user,
        ]);
    }

    public function bannedUser(User $user)
    {
        $user->status = UserStates::SUSPENDED;
        $user->active = false;
        $user->save();
        return $this->successResponse(data: [
            'banned_user' => $user,
        ]);
    }
    public function activateUser(User $user)
    {
        $user->status = UserStates::ACTIVE;
        $user->active = true;
        $user->save();
        return $this->successResponse(data: [
            'activated_user' => $user,
        ]);
    }

    public function stats(): JsonResponse
    {
        return $this->successResponse(data: [
            'users_stats' => [
                'all_users' => User::all()->count(),
                'active_users' => User::query()->where('status', UserStates::ACTIVE)->get()->count(),
                'inactive_users' => User::query()->where('status', UserStates::INACTIVE)->get()->count(),
                'banned_users' => User::query()->where('status', UserStates::SUSPENDED)->count(),
            ],
            'user_reg_byMonth' => $this->getUserRegistrationsByMonth(),
            'user_reg_byYear' => $this->getUserRegistrationsByYear(),
            'active_users_charts' => [
                'active_users_byMonth' => $this->getActiveUsersByMonth(),
                'active_users_byYear' => $this->getActiveUsersByYear(),
            ]
        ]);
    }

    public function getUserRegistrationsByMonth()
    {
        // Query the database to get user registrations grouped by month and year
        $registrations = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare the data for JSON response
        $months = [];
        $counts = [];

        foreach ($registrations as $registration) {
            $months[] = \Carbon\Carbon::createFromDate($registration->year, $registration->month, 1)->format('F Y');
            $counts[] = $registration->total;
        }

        // Return the data as a JSON response
        return [
            'months' => $months,
            'counts' => $counts,
        ];
    }
    public function getUserRegistrationsByYear()
    {
        $registrations = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereNotNull('created_at') // Ensure the created_at field is not null
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy('year', 'asc')
            ->get();

        $years = [];
        $counts = [];

        foreach ($registrations as $registration) {
            $years[]  = Carbon::parse($registration->created_at)->format('Y');
            $counts[] = $registration->total;
        }

        return [
            'years' => $years,
            'counts' => $counts,
        ];
    }
    public function getActiveUsersByMonth()
    {
        $activeUsers = User::select(
            DB::raw('YEAR(updated_at) as year'),
            DB::raw('MONTH(updated_at) as month'),
            DB::raw('count(*) as total')
        )
            ->where('updated_at', '>=', Carbon::now()->subMonth()) // Users who logged in in the last month
            ->groupBy(DB::raw('YEAR(updated_at)'), DB::raw('MONTH(updated_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $months = [];
        $counts = [];

        foreach ($activeUsers as $user) {
            $monthYear = Carbon::createFromFormat('Y-m', $user->year . '-' . $user->month)->format('F Y');
            $months[] = $monthYear;
            $counts[] = $user->total;
        }

        return [
            'months' => $months,
            'active_users' => $counts,
        ];
    }
    public function getActiveUsersByYear()
    {
        // Define the date range (last 5 years for example)
        $startDate = Carbon::now()->subYears(5); // 5 years ago
        $endDate = Carbon::now(); // Current date

        // Query to get active users by year
        $activeUsers = User::select(
            DB::raw('YEAR(updated_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('updated_at', [$startDate, $endDate]) // Filter by date range
            ->groupBy(DB::raw('YEAR(updated_at)')) // Group by year only
            ->orderBy('year', 'asc') // Order results by year ascending
            ->get();

        $years = [];
        $counts = [];

        foreach ($activeUsers as $user) {
            $years[] = $user->year;
            $counts[] = $user->total;
        }

        return [
            'years' => $years,
            'active_users' => $counts,
        ];
    }
}
