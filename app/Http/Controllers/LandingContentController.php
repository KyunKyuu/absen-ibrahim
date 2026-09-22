<?php

namespace App\Http\Controllers;

use App\Models\LandingItem;
use App\Models\LandingPage;
use App\Models\LandingTuitionPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LandingContentController extends Controller
{
    public function index()
    {
        $kinds = $this->kindLabels();
        $itemCounts = LandingItem::query()
            ->selectRaw('kind, count(*) as total, sum(case when is_active = 1 then 1 else 0 end) as active_total')
            ->groupBy('kind')
            ->get()
            ->keyBy('kind');

        return view('admin.landing.index', [
            'page' => $this->page(),
            'kinds' => $kinds,
            'itemCounts' => $itemCounts,
            'tuitionCount' => LandingTuitionPackage::query()->count(),
            'activeTuitionCount' => LandingTuitionPackage::query()->where('is_active', true)->count(),
        ]);
    }

    public function section(string $section)
    {
        abort_unless(in_array($section, ['hero', 'profile', 'admission'], true), 404);

        return view('admin.landing.section', [
            'page' => $this->page(),
            'section' => $section,
        ]);
    }

    public function content(string $kind)
    {
        $kinds = $this->kindLabels();
        abort_unless(array_key_exists($kind, $kinds), 404);

        return view('admin.landing.content', [
            'kind' => $kind,
            'kindLabel' => $kinds[$kind],
            'items' => LandingItem::query()->where('kind', $kind)->orderBy('sort_order')->get(),
        ]);
    }

    public function tuition()
    {
        return view('admin.landing.tuition', [
            'tuitionPackages' => LandingTuitionPackage::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function updateSection(Request $request, string $section)
    {
        $rules = match ($section) {
            'hero' => [
                'eyebrow' => ['required', 'string', 'max:80'],
                'headline' => ['required', 'string', 'max:160'],
                'intro' => ['required', 'string', 'max:600'],
                'hero_image_url' => ['nullable', 'url:http,https', 'max:2048'],
                'primary_cta_label' => ['required', 'string', 'max:50'],
                'primary_cta_url' => ['required', 'string', 'max:2048', 'regex:/^(#|\/|https?:\/\/)/i'],
                'secondary_cta_label' => ['required', 'string', 'max:50'],
                'secondary_cta_url' => ['required', 'string', 'max:2048', 'regex:/^(#|\/|https?:\/\/)/i'],
            ],
            'profile' => [
                'about_title' => ['required', 'string', 'max:160'],
                'about_body' => ['required', 'string', 'max:1500'],
            ],
            'admission' => [
                'admission_title' => ['required', 'string', 'max:160'],
                'admission_body' => ['required', 'string', 'max:1000'],
                'whatsapp' => ['nullable', 'string', 'max:30'],
                'email' => ['nullable', 'email', 'max:255'],
                'address' => ['nullable', 'string', 'max:600'],
                'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
            ],
            default => abort(404),
        };

        $this->page()->update($request->validate($rules));

        return back()->with('status', 'Perubahan berhasil disimpan.');
    }

    public function updatePage(Request $request)
    {
        $data = $request->validate([
            'eyebrow' => ['required', 'string', 'max:80'],
            'headline' => ['required', 'string', 'max:160'],
            'intro' => ['required', 'string', 'max:600'],
            'hero_image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'primary_cta_label' => ['required', 'string', 'max:50'],
            'primary_cta_url' => ['required', 'string', 'max:2048', 'regex:/^(#|\/|https?:\/\/)/i'],
            'secondary_cta_label' => ['required', 'string', 'max:50'],
            'secondary_cta_url' => ['required', 'string', 'max:2048', 'regex:/^(#|\/|https?:\/\/)/i'],
            'about_title' => ['required', 'string', 'max:160'],
            'about_body' => ['required', 'string', 'max:1500'],
            'admission_title' => ['required', 'string', 'max:160'],
            'admission_body' => ['required', 'string', 'max:1000'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:600'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        LandingPage::query()->firstOrCreate([], LandingPage::defaults())->update($data);

        return back()->with('status', 'Konten utama landing page disimpan.');
    }

    public function storeItem(Request $request)
    {
        LandingItem::query()->create($this->validateItem($request));

        return back()->with('status', 'Konten landing page ditambahkan.');
    }

    public function updateItem(Request $request, LandingItem $landingItem)
    {
        $landingItem->update($this->validateItem($request));

        return back()->with('status', 'Konten landing page diperbarui.');
    }

    public function destroyItem(LandingItem $landingItem)
    {
        $landingItem->delete();

        return back()->with('status', 'Konten landing page dihapus.');
    }

    public function storeTuitionPackage(Request $request)
    {
        LandingTuitionPackage::query()->create($this->validateTuitionPackage($request));

        return back()->with('status', 'Paket biaya pendidikan ditambahkan.');
    }

    public function updateTuitionPackage(Request $request, LandingTuitionPackage $tuitionPackage)
    {
        $tuitionPackage->update($this->validateTuitionPackage($request));

        return back()->with('status', 'Paket biaya pendidikan diperbarui.');
    }

    public function destroyTuitionPackage(LandingTuitionPackage $tuitionPackage)
    {
        $tuitionPackage->delete();

        return back()->with('status', 'Paket biaya pendidikan dihapus.');
    }

    private function validateItem(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(LandingItem::KINDS)],
            'title' => ['required', 'string', 'max:160'],
            'kicker' => ['nullable', 'string', 'max:100'],
            'body' => ['nullable', 'string', 'max:1500'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function validateTuitionPackage(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'billing_period' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:600'],
            'features_text' => ['nullable', 'string', 'max:3000'],
            'cta_label' => ['required', 'string', 'max:50'],
            'cta_url' => ['nullable', 'string', 'max:2048', 'regex:/^(#|\/|https?:\/\/)/i'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $data['features'] = collect(preg_split('/\r\n|\r|\n/', $data['features_text'] ?? ''))
            ->map(fn (string $feature) => trim($feature))
            ->filter()
            ->values()
            ->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['features_text']);

        return $data;
    }

    private function kindLabels(): array
    {
        return [
            'program' => 'Program unggulan',
            'activity' => 'Kabar & kegiatan',
            'testimonial' => 'Testimoni',
            'statistic' => 'Statistik',
        ];
    }

    private function page(): LandingPage
    {
        return LandingPage::query()->firstOrCreate([], LandingPage::defaults());
    }
}
