<?php
declare(strict_types=1);

use App\Models\Order;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Order::class, function () {
    return [
        'total' => 0,
    ];
});
