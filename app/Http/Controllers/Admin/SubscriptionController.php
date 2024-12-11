<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Services\Admin\SubscriptionService;
use Illuminate\Contracts\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function plans(): View
    {
        return $this->subscriptionService->planView();
    }
}
