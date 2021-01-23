<?php
declare(strict_types=1);

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Voucher::class, function () {
    return [
        'amount_original' => 100,
        'amount_remaining' => 100,
    ];
});
