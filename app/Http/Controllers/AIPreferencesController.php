<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIPreferencesController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('ai-preferences.show', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ia_temperature' => 'required|numeric|between:0.1,1.0|in:0.3,0.5,0.8',
        ]);

        auth()->user()->update([
            'ia_temperature' => $request->ia_temperature,
        ]);

        return back()->with('success', 'Preferências de IA atualizadas com sucesso!');
    }
}
