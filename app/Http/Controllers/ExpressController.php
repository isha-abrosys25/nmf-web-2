<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpressController extends Controller
{
    /**
     * Show the express page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // This will look for a file named 'express.blade.php'
        // in the 'resources/views/' directory.
        return view('express');
    }
}
