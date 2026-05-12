<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->getRoleNames()->first();
        if ($role) {
            return redirect("/{$role}");
        }
    }
    return view('welcome');
});
