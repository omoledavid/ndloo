<?php

use App\Models\GeneralSetting;
use App\Models\Setting;
use App\Notify\Notify;
use Illuminate\Support\Facades\Cache;

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
        return @$general->value;
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
