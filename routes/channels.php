<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Public channels don't need authorization
Broadcast::channel('livestream.{id}', function () {
    return true;
});

Broadcast::channel('livestreams', function () {
    return true;
});

// For private channels, you would use something like this:
// Broadcast::channel('private-livestream.{id}', function ($user, $id) {
//     return true; // Or some authorization logic
// });