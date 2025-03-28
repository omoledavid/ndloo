<?php

use App\Models\GeneralSetting;
use App\Models\Setting;
use App\Notify\Notify;
use Firebase\JWT\JWT;
use GetStream\Stream\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) {
        return @$general->$key;
    }

    return $general;
}

function ss($key = null)
{
    $general = Setting::query()->where('item', $key)->first();
    if($general)
    {
        return @$general->value;
    }
    return 0;
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $clickValue = null)
{
    $globalShortCodes = [
        'site_name' => gs('site_name'),
        'site_currency' => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object)$user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->clickValue = $clickValue;
    $notify->send();
}

function generateCallToken($userId) {
    $apiKey = env('STREAM_API_KEY');
    $apiSecret = env('STREAM_API_SECRET');
    $payload = [
        'user_id' => $userId,
        'iat' => time(),
        'exp' => time() + 3600, // 1-hour expiration
    ];

    return JWT::encode($payload, $apiSecret, 'HS256');
}

function getStreamToken($user)
{
    $client = new Client(env('STREAM_API_KEY'), env('STREAM_API_SECRET'));
    return $response = $client->createUserToken($user->id);
}
