<?php

namespace App\Http\Controllers;

use App\Models\LandingItem;
use App\Models\LandingPage;
use App\Models\LandingTuitionPackage;
use App\Models\SchoolSetting;

class LandingPageController extends Controller
{
    private function getLandingData(): array
    {
        $items = LandingItem::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get()
            ->groupBy('kind');
        $page = LandingPage::current();

        return [
            'page' => $page,
            'school' => SchoolSetting::active(),
            'programs' => $items->get('program', collect()),
            'activities' => $items->get('activity', collect()),
            'testimonials' => $items->get('testimonial', collect()),
            'statistics' => $items->get('statistic', collect()),
            'champions' => $items->get('champion', collect()),
            'tuitionPackages' => LandingTuitionPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ];
    }

    public function __invoke()
    {
        return view('landing', $this->getLandingData());
    }

    public function story()
    {
        return view('landing-story', $this->getLandingData());
    }
}
