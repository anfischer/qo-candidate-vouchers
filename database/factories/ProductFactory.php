<?php
declare(strict_types=1);

use App\Models\Product;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Product::class, function () {
    return [
        'price_each' => 100,
        'description' => 'Test product',
    ];
});
