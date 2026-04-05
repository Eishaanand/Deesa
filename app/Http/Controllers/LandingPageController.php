<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'examCount' => Exam::count(),
        ]);
    }
}
