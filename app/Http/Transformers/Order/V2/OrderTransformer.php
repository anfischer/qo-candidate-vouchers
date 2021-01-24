<?php
declare(strict_types=1);

namespace App\Http\Transformers\Order\V2;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

final class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order): array
    {
        // If the order has any vouchers on its belongs to many relation
        // the ids of the vouchers can be returned as is.
        // Otherwise we wrap the old, API v1 voucher belongs to relation, in an array an return it to
        // match the API v2 response.
        if ($order->vouchers->count() > 0) {
            $vouchers = $order->vouchers->pluck('id')->toArray();
        } else {
            $vouchers = [optional($order->voucher)->id];
        }

        return [
            'id' => (int) $order->id,
            'vouchers' => $vouchers,
            'total' => (int) $order->total,
        ];
    }
}
