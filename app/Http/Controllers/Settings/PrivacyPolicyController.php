<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $policy = PrivacyPolicy::first();

        if (!$policy) {
            $policy = PrivacyPolicy::create([
                'content' => '<p>Belum ada Privacy Policy yang ditulis.</p>',
            ]);
        }

        return Inertia::render('Settings/PrivacyPolicy', [
            'policy' => $policy,
            'can' => ['edit' => true],
        ]);
    }

public function update(Request $request)
{
    $validated = $request->validate([
        'content' => 'required|string',
    ]);

    $policy = PrivacyPolicy::first();

    if ($policy) {
        // update existing policy
        $policy->update([
            'content' => $validated['content'],
        ]);
    } else {
        // create new policy if none exists
        PrivacyPolicy::create($validated);
    }

    return redirect()
        ->route('privacy.index')
        ->with('success', '✅ Privacy Policy updated successfully.');
}

}
