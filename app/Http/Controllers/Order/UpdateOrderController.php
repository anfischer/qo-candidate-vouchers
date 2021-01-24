<?php

namespace App\Http\Controllers\Order;

use App\Commands\ProvideApiResponseForContractCommand;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class UpdateOrderController extends Controller
{
    private CommandBusInterface $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;

        $this->commandBus->addHandler(
            ProvideApiResponseForContractCommand::class,
            ApiResponseProviderService::class,
        );
    }

    public function __invoke(Order $order, UpdateOrderRequest $request): JsonResponse
    {
        // Commit order creation is transaction to ensure
        // the order will not be saved if attaching vouchers
        // or calculation and updating the total fails.
        DB::transaction(static function () use ($order, $request) {
            $order->total = $request->get('total', 0);
            // No need to keep voucher id reference, if any, after order update
            // since vouchers are now a many to many relation to the order.
            $order->voucher_id = null;
            $order->vouchers()->sync($request->vouchers());
            
            $order->calculateTotal();

            $order->save();
        });

        /** @var JsonResponse */
        $response = $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($order->fresh()));

        return $response;
    }
}
