<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

final class PolicyController extends Controller
{
    /**
     * Privacy page.
     *
     * @return View
     */
    public function privacy(): View
    {
        return view('policies.privacy');
    }

    /**
     * Terms of services page.
     *
     * @return View
     */
    public function tos(): View
    {
        return view('policies.tos');
    }
}
