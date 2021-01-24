<?php

namespace App\Http\Controllers\Order;

use App\Commands\ProvideApiResponseForContractCommand;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
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
        $order->fill($request->all());
        $order->calculateTotal();
        $order->saveOrFail();

        /** @var JsonResponse */
        $response = $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($order->fresh()));

        return $response;
    }
}
