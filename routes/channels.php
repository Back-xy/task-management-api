<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Register broadcasting routes with Sanctum authentication middleware
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Define authorization rules for private channel: user.{userId}
Broadcast::channel('user.{userId}', function (User $user, $userId) {
    // Only allow the user to listen to their own channel
    return $user->id === (int) $userId;
});
