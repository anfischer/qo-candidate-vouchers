<?php

namespace App\Http\Controllers\Order;

use App\Commands\ProvideApiResponseForContractCommand;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class CreateOrderController extends Controller
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

    public function __invoke(CreateOrderRequest $request): JsonResponse
    {
        $order = new Order([
            'total' => $request->get('total', 0),
        ]);

        // Commit order creation in transaction to ensure
        // the order will not be saved if attaching vouchers
        // or calculation and updating the total fails.
        DB::transaction(static function () use ($order, $request) {
            $order->save();
            $order->vouchers()->attach($request->vouchers());

            $order->calculateTotal();

            $order->save();
        });

        /** @var JsonResponse */
        $response = $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($order->fresh()));
        $response->setStatusCode(201);

        return $response;
    }
}
