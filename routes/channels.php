<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // Log the access attempt
    Log::info('Broadcast channel access attempt', ['user_id' => $user->id, 'channel_id' => $id]);

    // Validate that the ID is an integer
    if (!is_numeric($id) || (int) $id != $id) {
        return false;
    }

    // Check if the authenticated user ID matches the channel ID
    return (int) $user->id === (int) $id;
});