<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mode;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function mode()
    {
        $modes = Mode::where('is_active', true)->orderBy('id')->get();
        $currentModeId = Setting::getInt('mode');

        return view('admin.setting.mode', compact('modes', 'currentModeId'));
    }
    
    public function updateMode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode_id' => ['required', Rule::exists('mode_m', 'id')],
        ]);

        $mode = Mode::find($validated['mode_id']);

        Setting::set('mode', $mode->id, 'Mode transaksi aktif saat ini. Merujuk ke id pada tabel mode_m.');

        return response()->json([
            'success' => true,
            'message' => "Mode berhasil diubah menjadi \"{$mode->mode}\".",
            'mode_id' => $mode->id,
        ]);
    }
}