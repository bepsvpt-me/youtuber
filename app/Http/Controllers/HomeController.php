<?php

namespace App\Http\Controllers;

use App\Channel;
use Illuminate\View\View;

final class HomeController extends Controller
{
    /**
     * Home page.
     *
     * @return View
     */
    public function index(): View
    {
        $channels = Channel::query()
            ->where('hidden', '=', false)
            ->get();

        return view('home', compact('channels'));
    }
}
