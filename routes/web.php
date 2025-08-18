<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use Pusher\Pusher;


Route::get('/pusher-test', function () {
    $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        ]
    );

    $pusher->trigger('private-chat.14', 'MessageSent', [
        'message' => 'Test event from route'
    ]);

    return 'Event sent!';
});