<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrendingController extends Controller
{
    /**
     * Trending page.
     *
     * @return View
     */
    public function index(): View
    {
        $videos = DB::table('trendings')
            ->orderByDesc('fetched_at')
            ->orderBy('ranking')
            ->take(50)
            ->get();

        return view('trending', compact('videos'));
    }

    /**
     * Trending specific time page.
     *
     * @param string $time
     *
     * @return View
     */
    public function show(string $time): View
    {
        try {
            $carbon = Carbon::parse($time)->setSecond(0);

            abort_if($carbon->isFuture(), 404);

            $carbon->subMinutes($carbon->minute % 15);
        } catch (Exception $e) {
            abort(404);
        }

        $lower = $carbon->subMinutes(1)->format('Y-m-d H:i:s');

        $upper = $carbon->addMinutes(6)->format('Y-m-d H:i:s');

        $videos = DB::table('trendings')
            ->whereBetween('fetched_at', [$lower, $upper])
            ->orderByDesc('fetched_at')
            ->orderBy('ranking')
            ->take(50)
            ->get();

        return view('trending', compact('videos'));
    }
}
