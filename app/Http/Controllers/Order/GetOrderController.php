<?php

namespace App\Http\Controllers\Order;

use App\Commands\ProvideApiResponseForContractCommand;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class GetOrderController extends Controller
{
    private CommandBusInterface $commandBus;
    private ResponseFactory $responseFactory;

    public function __construct(
        CommandBusInterface $commandBus,
        ResponseFactory $responseFactory
    ) {
        $this->commandBus = $commandBus;
        $this->responseFactory = $responseFactory;

        $this->commandBus->addHandler(
            ProvideApiResponseForContractCommand::class,
            ApiResponseProviderService::class,
        );
    }

    public function __invoke(Order $order): JsonResponse
    {
        try {
            /** @var JsonResponse */
            return $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($order));
        } catch (InvalidArgumentException $e) {
            return $this->responseFactory->json(['error' => [
                'type' => 'ApiVersionException',
                'message' => $e->getMessage(),
            ]], 400);
        }
    }
}
