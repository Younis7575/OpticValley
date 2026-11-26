<?php

namespace Tests\Unit;

use App\Models\Coupon;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
{
    public function test_percent_discount_respects_cap(): void
    {
        $c = new Coupon(['type' => 'percent', 'value' => 10, 'max_discount' => 500]);
        $this->assertEquals(300.0, $c->discountFor(3000));
        $this->assertEquals(500.0, $c->discountFor(9000));
    }

    public function test_fixed_discount_never_exceeds_subtotal(): void
    {
        $c = new Coupon(['type' => 'fixed', 'value' => 1000]);
        $this->assertEquals(400.0, $c->discountFor(400));
    }
}
