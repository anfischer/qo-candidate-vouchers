<?php

namespace App\Http\Controllers\Order;

use App\Commands\ProvideApiResponseForContractCommand;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
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

    public function __invoke(Order $order, Request $request): JsonResponse
    {
        $order->fill($request->all());
        $order->calculateTotal();
        $order->saveOrFail();

        /** @var JsonResponse */
        $response = $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($order->fresh()));

        return $response;
    }
}
