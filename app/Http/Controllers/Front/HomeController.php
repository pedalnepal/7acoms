<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // SEO
        $title            = optional(Setting::where('name', 'meta_title')->first())->value ?: config('app.name', '');
        $meta_description = optional(Setting::where('name', 'meta_description')->first())->value ?: '';

        // Hero slider
        $sliders = Slider::with('media')
            ->where('status', 1)
            ->orderBy('menu_order', 'desc')
            ->take(5)
            ->get();

        return view('front.home', compact(
            'title',
            'meta_description',
            'sliders',
        ));
    }
}
