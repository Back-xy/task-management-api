<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;


Broadcast::routes(['middleware' => ['auth:sanctum']]);

Broadcast::channel('user.{userId}', function (User $user, $userId) {
    return $user->id === (int) $userId;
});
