<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $guarded = [];

    public static function defaults(): array
    {
        return [
            'eyebrow' => 'Sekolah Islam Terpadu',
            'headline' => 'Tumbuh dalam iman, ilmu, dan adab.',
            'intro' => 'Ruang belajar yang menumbuhkan rasa ingin tahu, kemandirian, dan akhlak baik—setiap hari.',
            'primary_cta_label' => 'Informasi Pendaftaran',
            'primary_cta_url' => '#pendaftaran',
            'secondary_cta_label' => 'Jelajahi Program',
            'secondary_cta_url' => '#program',
            'about_title' => 'Pendidikan yang dekat dengan kehidupan',
            'about_body' => 'Kami memadukan pembelajaran akademik, pembiasaan ibadah, dan pengalaman nyata agar setiap anak mengenali potensi serta tanggung jawabnya.',
            'admission_title' => 'Mari bertumbuh bersama kami',
            'admission_body' => 'Kenali lingkungan belajar, program, dan proses penerimaan peserta didik baru Sekolah Ibrahim.',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrNew([], static::defaults());
    }
}
