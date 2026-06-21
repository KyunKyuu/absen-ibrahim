<?php

namespace Tests\Unit;

use App\Services\PointCalculationService;
use PHPUnit\Framework\TestCase;

class PointCalculationServiceTest extends TestCase
{
    public function test_it_calculates_labels_from_general_points(): void
    {
        $service = new PointCalculationService;

        $this->assertSame('Teladan', $service->labelFor(120));
        $this->assertSame('Berkembang Baik', $service->labelFor(50));
        $this->assertSame('Perlu Dipantau', $service->labelFor(0));
        $this->assertSame('Perlu Pembinaan', $service->labelFor(-20));
        $this->assertSame('Prioritas Perhatian Guru', $service->labelFor(-50));
    }
}
