<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrivacyPolicyController extends Controller
{
public function index()
    {
       $model = PrivacyPolicy::first();
       $content = $model ? $model->content : '<p>Belum ada Privacy Policy yang ditulis.</p>';
        return Inertia::render('PrivacyPolicy', [
            'content' => $content
        ]);
    }
}
