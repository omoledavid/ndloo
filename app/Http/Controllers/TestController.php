<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testMail(Request $request)
    {
        $user = auth()->user();
        notify($user, 'WELCOME', ['email']);
        return 'testing mail';
    }
}
