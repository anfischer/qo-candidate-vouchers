<?php

namespace App\Http\Controllers\Order;

use App\Collections\OrderCollection;
use App\Commands\ProvideApiResponseForContractCommand;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class GetOrdersController extends Controller
{
    private CommandBusInterface $commandBus;
    private ResponseFactory $responseFactory;

    public function __construct(
        CommandBusInterface $commandBus,
        ResponseFactory $responseFactory
    ) {
        $this->commandBus = $commandBus;

        $this->commandBus->addHandler(
            ProvideApiResponseForContractCommand::class,
            ApiResponseProviderService::class,
        );
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(): JsonResponse
    {
        /** @var OrderCollection $orders */
        $orders = Order::all();

        try {
            /** @var JsonResponse */
            return $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($orders));
        } catch (InvalidArgumentException $e) {
            return $this->responseFactory->json(['error' => [
                'type' => 'ApiVersionException',
                'message' => $e->getMessage(),
            ]], 400);
        }
    }
}
