<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Kitchen Display Channel: only Koki & Admin
Broadcast::channel('kitchen', function (User $user) {
    return $user->hasAnyRole(['koki', 'admin']);
});

// POS Cashier Channel: only Staff & Admin
Broadcast::channel('pos', function (User $user) {
    return $user->hasAnyRole(['staff', 'admin']);
});

// Admin Alerts Channel: only Admin
Broadcast::channel('admin-alerts', function (User $user) {
    return $user->hasRole('admin');
});
