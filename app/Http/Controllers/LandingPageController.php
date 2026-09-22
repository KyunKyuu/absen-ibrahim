<?php

namespace App\Http\Controllers;

use App\Models\LandingItem;
use App\Models\LandingPage;
use App\Models\LandingTuitionPackage;
use App\Models\SchoolSetting;

class LandingPageController extends Controller
{
    public function __invoke()
    {
        $items = LandingItem::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get()
            ->groupBy('kind');

        return view('landing', [
            'page' => LandingPage::current(),
            'school' => SchoolSetting::active(),
            'programs' => $items->get('program', collect()),
            'activities' => $items->get('activity', collect()),
            'testimonials' => $items->get('testimonial', collect()),
            'statistics' => $items->get('statistic', collect()),
            'tuitionPackages' => LandingTuitionPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
