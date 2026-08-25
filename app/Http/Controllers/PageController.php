<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\SuccessStory;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'stories' => SuccessStory::published()->latest()->take(3)->get(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function modules(): View
    {
        return view('pages.modules');
    }

    public function faqs(): View
    {
        return view('pages.faqs', [
            'faqs' => Faq::published()->get()->groupBy('category'),
        ]);
    }
}
