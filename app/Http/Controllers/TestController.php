<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testMail(Request $request)
    {
        $user = User::query()->where('id', '9c8cb9dc-14fa-4ec2-8996-ac8859a1f4e3')->first();
        return [
            'user' => $user,
            'token' => getStreamToken($user),
            'data' => createGetStreamUser($user)
        ];
    }
}
