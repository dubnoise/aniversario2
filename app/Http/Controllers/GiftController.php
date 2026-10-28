<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class GiftController extends Controller
{
    // Al entrar a la raíz, exigimos login (olvidamos la sesión anterior)
    public function index(Request $request)
    {
        $request->session()->forget('authenticated');
        return view('login/login');
    }

    // Procesa el código secreto
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);

        if ($request->code === config('reasons.access_code')) {
            $request->session()->put('authenticated', true);
            // Guardamos la hora exacta en que se desbloqueó el regalo por primera vez
            if (!$request->session()->has('unlock_start')) {
                $request->session()->put('unlock_start', now());
            }
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['code' => 'Pista: ¡Es la fecha de nuestro aniversario! (DDMM)']);
    }

    // 🌟 Nueva página principal tras el login
    public function dashboard(Request $request)
    {
        if (!$request->session()->get('authenticated')) {
            return redirect()->route('gift');
        }

        $startDate = Carbon::parse(config('reasons.start_date', '2024-11-29'));
        $diff = $startDate->diff(now());
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        return view('dashboard', compact('years', 'months', 'days'));
    }

    // Muestra el panel con las tarjetas bloqueadas/desbloqueadas temporalmente
    public function board(Request $request)
    {
        if (!$request->session()->get('authenticated')) {
            return redirect()->route('gift');
        }

        $reasons = config('reasons.list');
        $unlockStart = Carbon::parse($request->session()->get('unlock_start', now()));

        // 📅 Fecha de inicio de la relación
        $startDate = Carbon::parse(config('reasons.start_date', '2024-11-29'));
        $now = now();

        // Desglose exacto sin decimales en años, meses y días
        $diff = $startDate->diff($now);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        foreach ($reasons as $index => &$reason) {
            // $unlockTime = $unlockStart->copy()->addHours($index * 24);
            $unlockTime = $unlockStart->copy()->addHours($index * 0.05);
            $isUnlocked = $now->greaterThanOrEqualTo($unlockTime);

            $reason['unlocked'] = $isUnlocked;
            $reason['seconds_left'] = $isUnlocked ? 0 : (int) round($now->diffInSeconds($unlockTime, false));
        }

        return view('gift', compact('reasons', 'years', 'months', 'days'));
    }
    public function map(Request $request)
    {
        if (!$request->session()->get('authenticated')) {
            return redirect()->route('gift');
        }

        $places = config('places.list');

        return view('map', compact('places'));
    }
    public function safe(Request $request)
    {
        if (!$request->session()->get('authenticated')) {
            return redirect()->route('gift');
        }

        return view('safe');
    }
}
