<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show()
    {
        return auth()->user()->only([
            'telegram_token',
            'telegram_chat_id'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'telegram_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
        ]);

        $user = auth()->user();

        $user->update([
            'telegram_token' => $request->telegram_token,
            'telegram_chat_id' => $request->telegram_chat_id,
        ]);

        return response()->json(['ok' => true]);
    }
}