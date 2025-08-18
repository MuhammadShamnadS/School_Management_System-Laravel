<?php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:api']]); // JWT-protected auth endpoint

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    // Only allow the owner of the channel to listen
    return (int) $user->id === (int) $userId;
});
