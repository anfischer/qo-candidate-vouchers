<?php
declare(strict_types=1);

namespace App\Http\Transformers\Order\V2;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

final class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order): array
    {
        return [
            'id' => (int) $order->id,
            'vouchers' => [$order->voucher_id],
            'total' => (int) $order->total,
        ];
    }
}
