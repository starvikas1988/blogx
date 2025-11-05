<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'users'    => \App\Models\User::count(),
            'posts'    => \App\Models\Post::count(),
            'comments' => \App\Models\Comment::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
