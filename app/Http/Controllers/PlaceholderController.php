<?php

namespace App\Http\Controllers;

class PlaceholderController extends Controller
{
    public function index(string $title = null)
    {
        return view('placeholder', ['title' => $title ?? 'Fitur dalam Pengembangan']);
    }
}