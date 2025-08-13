<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index() {
        return Inertia::render('Documents/Index', []);
    }

    public function pdf()
    {
        return Inertia::render('Documents/partials/Pdf', []);
    }

    public function gambar()
    {
        return Inertia::render('Documents/partials/Gambar', []);
    }

    public function doc()
    {
        return Inertia::render('Documents/partials/Doc', []);
    }
}
