<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Enums\UserStates;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Services\BaseService;

class ManageUsersController extends BaseService
{
    public function allUsers()
    {
        $allUsers = User::query()->get();
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
}
