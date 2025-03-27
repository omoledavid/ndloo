<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Enums\SubscriptionStatus;
use App\Contracts\Enums\UserStates;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\SubscriptionPlan;
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
                    ->orWhere('lastname', 'like', '%' . $request->name . '%')
                    ->orWhere('email', 'like', '%' . $request->name . '%');
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
        $allUsers = UserResource::collection($query->paginate(10));

        // Return the filtered users
        return $this->successResponse(data: [
            'all_users' => $allUsers,
            'pagination' => [
                'current_page' => $allUsers->currentPage(),
                'per_page' => $allUsers->perPage(),
                'total' => $allUsers->total(),
                'last_page' => $allUsers->lastPage(),
                'next_page_url' => $allUsers->nextPageUrl(),
                'prev_page_url' => $allUsers->previousPageUrl(),
            ],
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
    public function premiumAccess(Request $request)
    {
        $userIds = is_array($request->user_id) ? $request->user_id : [$request->user_id]; // Convert single ID to an array

        $users = User::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            return $this->errorResponse('No valid users found', 404);
        }

        // Detach subscriptions for each user
        foreach ($users as $user) {
            $user->subscriptions()->detach();
            $user->subscriptions()->attach(SubscriptionPlan::query()->where('is_default', SubscriptionStatus::ENABLE)->first());
        }

        return $this->successResponse('User(s) premium access granted', [
            'users' => UserResource::collection($users),
        ]);
    }

    public function premiumAccessRevoke(Request $request)
    {
        $userIds = is_array($request->user_id) ? $request->user_id : [$request->user_id]; // Convert single ID to an array

        $users = User::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            return $this->errorResponse('No valid users found', 404);
        }

        // Detach subscriptions for each user
        foreach ($users as $user) {
            $user->subscriptions()->detach();
        }

        return $this->successResponse('User(s) premium access revoked', [
            'users' => UserResource::collection($users),
        ]);
    }

    public function editUserInfo(User $user, Request $request)
    {
        $user->profile()->detach(array_keys($request->details));

        foreach ($request->details as $id => $content) {
            $request->user()->profile()->attach($id, ['content' => $content]);
        }
        return $this->successResponse('User Account info updated', [
            'user' => new UserResource($user)
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

        // Get registration data
        $registrations = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Reference array for months
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // Get the current year

        // Initialize result array with zero values
        $formattedRegistrations = [];
        foreach ($months as $num => $name) {
            $formattedRegistrations[$num] = [
                'label' => "$name",
                'value' => 0
            ];
        }

        // Populate with actual registration data
        foreach ($registrations as $registration) {
            $formattedRegistrations[$registration->month]['value'] = $registration->total;
        }

        // Convert back to indexed array
        $formattedRegistrations = array_values($formattedRegistrations);

        // Return JSON response
        return [
            'months' => array_column($formattedRegistrations, 'label'),
            'counts' => array_column($formattedRegistrations, 'value'),
        ];

    }
    public function getUserRegistrationsByYear()
    {
        // Define the date range (last 5 years)
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5;

        // Fetch user registrations grouped by year
        $registrations = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', '>=', $startYear) // Get records for the past 5 years
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy('year', 'asc')
            ->get();

        // Initialize an array for the past 5 years with default zero values
        $formattedRegistrations = [];
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $formattedRegistrations[$year] = [
                'year' => $year,
                'total' => 0
            ];
        }

        // Populate the array with actual registration data
        foreach ($registrations as $registration) {
            $formattedRegistrations[$registration->year]['total'] = $registration->total;
        }

        // Convert associative array to indexed array
        $formattedRegistrations = array_values($formattedRegistrations);

        // Return JSON response
        return [
            'years' => array_column($formattedRegistrations, 'year'),
            'counts' => array_column($formattedRegistrations, 'total'),
        ];
    }

    public function getActiveUsersByMonth()
    {
        $activeUsers = User::select(
            DB::raw('YEAR(updated_at) as year'),
            DB::raw('MONTH(updated_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('updated_at', '>=', Carbon::now()->subMonth()) // Users active in the last month
            ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];


        $formattedActiveUsers = [];
        foreach ($months as $num => $name) {
            $formattedActiveUsers[$num] = [
                'label' => "$name",
                'value' => 0
            ];
        }

        foreach ($activeUsers as $user) {
            $formattedActiveUsers[$user->month]['value'] = $user->total;
        }

        $formattedActiveUsers = array_values($formattedActiveUsers);

        return [
            'months' => array_column($formattedActiveUsers, 'label'),
            'active_users' => array_column($formattedActiveUsers, 'value'),
        ];
    }
    public function getActiveUsersByYear()
    {
        // Define the date range (last 5 years)
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5;

        // Fetch active users by year
        $activeUsers = User::select(
            DB::raw('YEAR(updated_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('updated_at', '>=', $startYear) // Get records for the past 5 years
            ->groupBy(DB::raw('YEAR(updated_at)'))
            ->orderBy('year', 'asc')
            ->get();

        // Initialize an array for the past 5 years with default zero values
        $formattedActiveUsers = [];
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $formattedActiveUsers[$year] = [
                'year' => $year,
                'total' => 0
            ];
        }

        // Populate the array with actual active user data
        foreach ($activeUsers as $user) {
            $formattedActiveUsers[$user->year]['total'] = $user->total;
        }

        // Convert associative array to indexed array
        $formattedActiveUsers = array_values($formattedActiveUsers);

        // Return JSON response
        return [
            'years' => array_column($formattedActiveUsers, 'year'),
            'active_users' => array_column($formattedActiveUsers, 'total'),
        ];
    }

}
