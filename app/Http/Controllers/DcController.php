<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DcController extends Controller
{
    public function dashboard(){
        return view('dc.dashboard');
    }
}
