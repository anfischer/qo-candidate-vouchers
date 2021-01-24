<?php
declare(strict_types=1);

namespace App\Http\Transformers\Order\V1;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

final class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order): array
    {
        return [
            'id' => (int) $order->id,
            'voucher_id' => $order->voucher_id ? (int) $order->voucher_id : null,
            'total' => (int) $order->total,
        ];
    }
}
