<?php
declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_all_order_lines_for_an_order(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->save();

        $response = $this->getJson("/api/orders/{$order->id}/order-lines");
        $response->assertOk();

        $this->assertCount(5, $response->json());
        $order->fresh()->lines()->each(function (OrderLine $orderLine, int $key) use ($response) {
            $this->assertEquals($orderLine->toArray(), $response->json()[$key]);
        });
    }

    /** @test */
    public function it_can_get_a_single_order_line(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $firstOrderLine = factory(OrderLine::class)->make();
        $order->lines()->save($firstOrderLine);
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->lines()->save(factory(OrderLine::class)->make());
        $order->save();

        $response = $this->getJson("/api/orders/{$order->id}/order-lines/{$firstOrderLine->id}");
        $response->assertOk();

        $this->assertEquals($firstOrderLine->toArray(), $response->json());
    }

    /** @test */
    public function it_can_create_an_order_line(): void
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();

        /** @var Order $order */
        $order = factory(Order::class)->create();
        $firstOrderLine = factory(OrderLine::class)->make();
        $order->lines()->save($firstOrderLine);

        $this->assertSame(0, $order->fresh()->total);
        $this->assertSame(100, $firstOrderLine->amount_total);

        $order->calculateTotal();
        $order->save();

        $this->assertSame($firstOrderLine->amount_total, $order->fresh()->total);

        $response = $this->postJson("/api/orders/{$order->id}/order-lines", [
            'product_id' => $product->id,
            'description' => 'Test order line 2',
            'amount_each' => 50,
            'amount_total' => 150,
            'quantity' => 3,
        ]);
        $response->assertCreated();

        $orderLines = OrderLine::all();
        $this->assertCount(2, $orderLines);
        $this->assertEquals($orderLines->last()->toArray(), $response->json());

        $this->assertSame(150, $orderLines->last()->amount_total);
        $this->assertSame(250, $order->fresh()->total);
    }

    /** @test */
    public function it_can_update_an_order_line(): void
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();

        /** @var Order $order */
        $order = factory(Order::class)->create();
        /** @var OrderLine $firstOrderLine */
        $firstOrderLine = factory(OrderLine::class)->make();
        $order->lines()->save($firstOrderLine);

        $this->assertSame(0, $order->fresh()->total);
        $this->assertSame(100, $firstOrderLine->amount_total);

        $order->calculateTotal();
        $order->save();

        $this->assertNotSame($product->id, $firstOrderLine->product->id);

        $response = $this->patchJson("/api/orders/{$order->id}/order-lines/{$firstOrderLine->id}", [
            'product_id' => $product->id,
            'description' => 'Test order line 2',
            'amount_each' => 75,
            'amount_total' => 300,
            'quantity' => 4,
        ]);
        $response->assertOk();

        $this->assertEquals([
            'id' => $firstOrderLine->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'description' => 'Test order line 2',
            'amount_each' => 75,
            'amount_total' => 300,
            'quantity' => 4,
        ], $response->json());

        $this->assertSame(300, $order->fresh()->total);
    }

    /** @test */
    public function it_can_delete_an_order_line(): void
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $firstOrderLine = factory(OrderLine::class)->make([
            'amount_each' => 50,
            'amount_total' => 150,
            'quantity' => 3,
        ]);
        $order->lines()->save($firstOrderLine);
        $order->calculateTotal();
        $order->save();

        $this->assertSame(150, $order->fresh()->total);

        $response = $this->deleteJson("/api/orders/{$order->id}/order-lines/{$firstOrderLine->id}");
        $response->assertOk();

        $orderLines = OrderLine::all();
        $this->assertCount(0, $orderLines);
        $this->assertSame(0, $order->fresh()->total);
    }
}
