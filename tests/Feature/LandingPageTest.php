<?php

namespace Tests\Feature;

use App\Models\LandingItem;
use App\Models\LandingTuitionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmin_can_open_landing_page_manager(): void
    {
        $teacher = User::query()->create([
            'name' => 'Guru', 'email' => 'guru-landing@test.test', 'role' => 'teacher',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);
        $superadmin = User::query()->create([
            'name' => 'Admin', 'email' => 'admin-landing@test.test', 'role' => 'superadmin',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);

        $teacherResponse = $this->actingAs($teacher)->get(route('admin.landing.index'));
        $this->assertSame(403, $teacherResponse->status(), (string) $teacherResponse->headers->get('Location'));
        $this->actingAs($superadmin)->get(route('admin.landing.index'))->assertOk()->assertSee('Tambah konten');
    }

    public function test_landing_manager_is_split_into_focused_pages(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin', 'email' => 'admin-sections@test.test', 'role' => 'superadmin',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);

        $this->actingAs($admin);

        $this->get(route('admin.landing.hero'))->assertOk()->assertSee('Hero &amp; tombol', false);
        $this->get(route('admin.landing.profile'))->assertOk()->assertSee('Profil sekolah');
        $this->get(route('admin.landing.admission'))->assertOk()->assertSee('Admisi &amp; kontak', false);
        $this->get(route('admin.landing.content', 'program'))->assertOk()->assertSee('Program unggulan');
        $this->get(route('admin.landing.tuition'))->assertOk()->assertSee('Tambah paket biaya');
        $this->get(route('admin.landing.content', 'unknown'))->assertNotFound();
    }

    public function test_superadmin_can_create_update_and_delete_landing_content(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin', 'email' => 'admin-crud@test.test', 'role' => 'superadmin',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.landing.items.store'), [
            'kind' => 'program',
            'title' => 'Kelas Bahasa Arab',
            'kicker' => 'Bahasa',
            'body' => 'Belajar bahasa melalui percakapan sehari-hari.',
            'sort_order' => 5,
            'is_active' => '1',
        ])->assertRedirect();

        $item = LandingItem::query()->where('title', 'Kelas Bahasa Arab')->firstOrFail();
        $this->assertTrue($item->is_active);

        $this->actingAs($admin)->put(route('admin.landing.items.update', $item), [
            'kind' => 'program',
            'title' => 'Bahasa Arab Aktif',
            'kicker' => 'Bahasa',
            'body' => 'Percakapan dan literasi.',
            'sort_order' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('landing_items', [
            'id' => $item->id, 'title' => 'Bahasa Arab Aktif', 'is_active' => false,
        ]);

        $this->actingAs($admin)->delete(route('admin.landing.items.destroy', $item))->assertRedirect();
        $this->assertDatabaseMissing('landing_items', ['id' => $item->id]);
    }

    public function test_inactive_content_is_not_shown_publicly(): void
    {
        LandingItem::query()->create([
            'kind' => 'program', 'title' => 'Konten Rahasia', 'sort_order' => 1, 'is_active' => false,
        ]);

        $this->get(route('landing'))->assertOk()->assertDontSee('Konten Rahasia');
    }

    public function test_superadmin_can_manage_a_tuition_package_and_publish_it(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin', 'email' => 'admin-tuition@test.test', 'role' => 'superadmin',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.landing.tuition.store'), [
            'name' => 'Paket Pendidikan',
            'price' => 1500000,
            'billing_period' => 'per bulan',
            'description' => 'Biaya pembelajaran bulanan.',
            'features_text' => "Tahfiz\nEkstrakurikuler",
            'cta_label' => 'Tanya paket',
            'cta_url' => '#pendaftaran',
            'sort_order' => 1,
            'is_featured' => '1',
            'is_active' => '1',
        ])->assertRedirect();

        $package = LandingTuitionPackage::query()->firstOrFail();
        $this->assertSame(['Tahfiz', 'Ekstrakurikuler'], $package->features);
        $this->get(route('landing'))
            ->assertOk()
            ->assertSee('Paket Pendidikan')
            ->assertSee('1.500.000');

        $this->actingAs($admin)->delete(route('admin.landing.tuition.destroy', $package))->assertRedirect();
        $this->assertDatabaseMissing('landing_tuition_packages', ['id' => $package->id]);
    }
}
