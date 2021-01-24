<?php
declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderV2Test extends TestCase
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

        $response = $this->getJson('/api/orders', [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);
        $response->assertOk();

        $this->assertCount(5, $response->json());
        $orders->each(function (Order $order, int $key) use ($response) {
            $this->assertEquals([
                'id' => $order->fresh()->id,
                'total' => 0,
                'vouchers' => [$order->fresh()->voucher_id],
            ], $response->json()[$key]);
        });
    }

    /** @test */
    public function it_can_get_a_single_order(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->voucher()->associate(factory(Voucher::class)->create());
        $order->save();

        $response = $this->getJson("/api/orders/{$order->id}", [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);
        $response->assertOk();

        $this->assertEquals([
            'id' => $order->fresh()->id,
            'total' => 0,
            'vouchers' => [$order->fresh()->voucher_id],
        ], $response->json());
    }

    /** @test */
    public function it_can_create_an_order_without_a_voucher(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson('/api/orders', [], [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);
        $response->assertCreated();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertEquals([
            'id' => $orders->first()->fresh()->id,
            'total' => 0,
            'vouchers' => [$orders->first()->fresh()->voucher_id],
        ], $response->json());
        $this->assertSame(0, $orders->first()->total);
        $this->assertNull($orders->first()->voucher);
    }

    /** @test */
    public function it_can_create_an_order_with_a_voucher(): void
    {
        $voucher = factory(Voucher::class)->create();

        $response = $this->postJson('/api/orders', [
            'voucher_id' => $voucher->id,
        ], [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);

        $response->assertCreated();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertCount(1, $orders->first()->vouchers);
        $this->assertEquals([
            'id' => $orders->first()->fresh()->id,
            'total' => 100,
            'vouchers' => [$voucher->id],
        ], $response->json());
        $this->assertSame(100, $orders->first()->total);
    }

    /** @test */
    public function it_can_create_an_order_with_multiple_vouchers(): void
    {
        $vouchers = factory(Voucher::class, 5)->create();

        $response = $this->postJson('/api/orders', [
            'voucher_ids' => $vouchers->pluck('id'),
        ], [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);

        $response->assertCreated();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertEquals([
            'id' => $orders->first()->fresh()->id,
            'total' => 500,
            'vouchers' => $orders->first()->fresh()->vouchers->pluck('id')->toArray(),
        ], $response->json());
        $this->assertSame(500, $orders->first()->total);
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
            'voucher_ids' => [$voucher->id],
        ], [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);
        $response->assertOk();

        $orders = Order::all();
        $this->assertCount(1, $orders);
        $this->assertCount(1, $orders->first()->vouchers);
        $this->assertEquals([
            'id' => $orders->first()->fresh()->id,
            'total' => 100,
            'vouchers' => [$voucher->id],
        ], $response->json());
        $this->assertSame(100, $order->fresh()->total);
    }

    /** @test */
    public function it_can_update_an_order_to_have_multiple_vouchers(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $this->assertSame(0, $order->total);

        $vouchers = factory(Voucher::class, 5)->create();

        $this->patchJson("/api/orders/{$order->id}", [
            'voucher_ids' => [$vouchers->first()->id],
        ], [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);

        $this->assertSame(100, $order->fresh()->total);
        $this->assertCount(1, $order->fresh()->vouchers);

        $response = $this->patchJson("/api/orders/{$order->id}", [
            'voucher_ids' => $vouchers->pluck('id')->toArray(),
        ], [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);

        $response->assertOk();
        $this->assertEquals([
            'id' => $order->fresh()->id,
            'total' => 500,
            'vouchers' => $order->fresh()->vouchers->pluck('id')->toArray(),
        ], $response->json());
        $this->assertSame(500, $order->fresh()->total);
        $this->assertCount(5, $order->fresh()->vouchers);
    }

    /** @test */
    public function it_can_delete_an_order(): void
    {
        $order = factory(Order::class)->create();

        $response = $this->deleteJson("/api/orders/{$order->id}", [
            'Accept' => 'application/vnd.quickorder.v2+json',
        ]);

        $response->assertOk();

        $orders = Order::all();
        $this->assertCount(0, $orders);
    }
}
