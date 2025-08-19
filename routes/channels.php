<?php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:api']]); // JWT-protected auth endpoint

Broadcast::channel('chat.{userOne}.{userTwo}', function ($user, $userOne, $userTwo) {
    // user must be either participant in the chat
    return (int) $user->id === (int) $userOne || (int) $user->id === (int) $userTwo;
});
