<?php
declare(strict_types=1);

namespace App\Collections;

use App\Models\OrderLine;
use Illuminate\Database\Eloquent\Collection;

final class OrderLineCollection extends Collection
{
    public function total(): int
    {
        return $this->sum(static function (OrderLine $orderLine) {
            return $orderLine->amount_total;
        });
    }
}
