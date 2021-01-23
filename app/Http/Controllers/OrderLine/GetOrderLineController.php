<?php

namespace App\Http\Controllers\OrderLine;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Routing\Controller;

class GetOrderLineController extends Controller
{
    public function __invoke(Order $order, OrderLine $orderLine) {
        return $orderLine;
    }
}
