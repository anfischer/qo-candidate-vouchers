<?php
declare(strict_types=1);

use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(OrderLine::class, function () {
    /** @var Product $product */
    $product = factory(Product::class)->create();

    return [
        'product_id' => $product->id,
        'description' => 'Test order line',
        'amount_each' => 100,
        'amount_total' => 100,
        'quantity' => 1,
    ];
});
