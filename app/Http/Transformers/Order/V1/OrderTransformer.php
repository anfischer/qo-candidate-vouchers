<?php
declare(strict_types=1);

namespace App\Http\Transformers\Order\V1;

use App\Models\Order;
use InvalidArgumentException;
use League\Fractal\TransformerAbstract;

final class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order): array
    {
        // If the order has more vouchers attached than one, we will break v1 API-spec by
        // returning an array of voucher ids. Throw an exception, which will be rendered into an error response later in the stack.
        if ($order->vouchers->count() > 1) {
            throw new InvalidArgumentException(
                'Order contains more than one attached voucher. To get orders with multiple vouchers attached, use api v2 or later.'
            );
        }

        // Otherwise, if the relation of vouchers contains a maximum of one voucher,
        // it can be returned in the old format,
        // or if the old voucher relation is present, the voucher id can be returned directly as is.
        $voucherId = null;

        if ($order->voucher) {
            $voucherId = (int) $order->voucher_id;
        } elseif ($order->vouchers->count() === 1) {
            $voucherId = (int) $order->vouchers->first()->id;
        }

        return [
            'id' => (int) $order->id,
            'voucher_id' => $voucherId,
            'total' => (int) $order->total,
        ];
    }
}
