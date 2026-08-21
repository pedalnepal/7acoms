<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;

class SitemapController extends Controller
{
    public function index()
    {
        // Static priority URLs (named public routes)
        $statics = [
            ['loc' => route('home'),                  'priority' => '1.0', 'freq' => 'daily'],
            ['loc' => route('about.acoms'),           'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('about.naoms'),           'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('organizing.committee'),  'priority' => '0.7', 'freq' => 'monthly'],
            ['loc' => route('registration.details'),  'priority' => '0.8', 'freq' => 'weekly'],
            ['loc' => route('registration.form'),     'priority' => '0.7', 'freq' => 'weekly'],
            ['loc' => route('abstract.submission'),   'priority' => '0.7', 'freq' => 'weekly'],
            ['loc' => route('contact.us'),            'priority' => '0.6', 'freq' => 'monthly'],
        ];

        // Permalinks already covered by the named static routes above -
        // skip them so CMS pages aren't listed twice.
        $named = [
            'about-acoms', 'about-naoms', 'organizing-committee',
            'registration-details', 'registration-form',
            'abstract-submission', 'contact-us',
        ];

        // CMS pages served by the page.detail catch-all (non-deleted).
        $pages = Page::whereNull('deleted_at')
            ->whereNotIn('permalink', $named)
            ->select('permalink', 'updated_at')
            ->get();

        return response()
            ->view('front.sitemap', compact('statics', 'pages'))
            ->header('Content-Type', 'application/xml');
    }
}
