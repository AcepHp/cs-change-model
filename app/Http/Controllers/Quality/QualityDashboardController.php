<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QualityDashboardController extends Controller
{
    public function index(){
        return view('quality.dashboard.index');
    }
}
