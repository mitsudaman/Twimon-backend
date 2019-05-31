<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OGPController extends Controller
{
    public function index($id = "")
    {
        return view('ogp/index', ['id' => $id]);
    }
}
