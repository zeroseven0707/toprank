<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ContentPlaceController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/ContentPlaceSettings', [
            'title' => 'Content & Place Settings',
        ]);
    }
}
