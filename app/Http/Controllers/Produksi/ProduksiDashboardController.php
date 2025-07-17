<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ProduksiDashboardController extends Controller
{
    public function index(){
        return view('produksi.dashboard.index');
    }
}
