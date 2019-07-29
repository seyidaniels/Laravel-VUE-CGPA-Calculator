<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\GPA;

use Auth;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function addGpa (Request $request) {
        $request->validate([
            'total_score' => 'numeric',
            'total_units' => 'numeric'
        ]);
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $gpa = GPA::create($data);
        $cgpa = Auth::user()->cpga();

        return response ()->json (
           [
            'message' => 'GPA saved successfully',
            'cgpa' => $cgpa
           ]
           );
    }
}
