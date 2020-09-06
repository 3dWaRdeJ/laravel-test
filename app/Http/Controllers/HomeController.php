<?php

namespace App\Http\Controllers;

use App\Position;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('home', ['user' => auth()->user()]);
    }

    public function position()
    {
        $positions = Position::all();
        return view('positions', [
            'positions' => $positions,
            'apiToken' => auth()->user()->api_token
        ]);
    }

    public function employee()
    {
        return view('employees', [
            'apiToken' => auth()->user()->{'api_token'}
        ]);
    }
}
