<?php
declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_all_orders(): void
    {
        $orders = factory(Order::class, 5)->create()->each(static function (Order $order, int $index) {
            if ($index > 0) {
                $order->voucher()->associate(factory(Voucher::class)->create());
                $order->save();
            }
        });

        $response = $this->getJson('/api/orders');
        $response->assertOk();

        $this->assertCount(5, $response->json());
        $orders->each(function (Order $order, int $key) use ($response) {
            $this->assertEquals($order->fresh()->toArray(), $response->json()[$key]);
        });
    }

    /** @test */
    public function it_can_get_a_single_order(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->voucher()->associate(factory(Voucher::class)->create());
        $order->save();

        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertOk();

        $this->assertEquals($order->toArray(), $response->json());
    }

    /** @test */
    public function it_can_create_an_order_without_a_voucher(): void
    {
        $response = $this->postJson('/api/orders');
        $response->assertCreated();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertEquals($orders->first()->toArray(), $response->json());
        $this->assertSame(0, $orders->first()->total);
        $this->assertNull($orders->first()->voucher);
    }

    /** @test */
    public function it_can_create_an_order_with_a_voucher(): void
    {
        $voucher = factory(Voucher::class)->create();

        $response = $this->postJson('/api/orders', [
            'voucher_id' => $voucher->id,
        ]);

        $response->assertCreated();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertEquals([
            'id' => $orders->first()->id,
            'total' => $orders->first()->total,
            'voucher_id' => $orders->first()->vouchers->first()->id,
        ], $response->json());
        $this->assertSame(100, $orders->first()->total);
    }

    /** @test */
    public function it_can_update_an_order(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $this->assertSame(0, $order->total);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create();

        $response = $this->patchJson("/api/orders/{$order->id}", [
            'voucher_id' => $voucher->id,
        ]);
        $response->assertOk();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertEquals([
            'id' => $orders->first()->id,
            'total' => $orders->first()->total,
            'voucher_id' => $orders->first()->vouchers->first()->id,
        ], $response->json());
        $this->assertSame(100, $order->fresh()->total);
    }

    /** @test */
    public function it_can_delete_an_order(): void
    {
        $order = factory(Order::class)->create();

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertOk();

        $orders = Order::all();
        $this->assertCount(0, $orders);
    }
}
