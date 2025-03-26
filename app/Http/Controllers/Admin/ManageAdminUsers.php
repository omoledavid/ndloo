<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminUserResource;
use App\Models\User;
use App\Support\Services\BaseService;
use Illuminate\Http\Request;

class ManageAdminUsers extends BaseService
{
    public function allAdminUsers()
    {
        $allAdminUsers = User::query()->where('type', UserType::ADMIN)->paginate();
        return $this->successResponse(data: [
            'admins' => AdminUserResource::collection($allAdminUsers),
        ]);
    }
    public function deleteAdminUsers(User $admin)
    {
        if($admin->delete())
        {
            return $this->successResponse('admin has been deleted successfully.');
        }else{
            return $this->errorResponse('admin has been deleted failed.');
        }

    }
}
