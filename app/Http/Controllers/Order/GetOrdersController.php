<?php

namespace App\Http\Controllers\Order;

use App\Collections\OrderCollection;
use App\Commands\ProvideApiResponseForContractCommand;
use App\Models\Order;
use App\Services\ApiResponseProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class GetOrdersController extends Controller
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

    public function __invoke()
    {
        /** @var OrderCollection $orders */
        $orders = Order::all();

        /** @var JsonResponse */
        return $this->commandBus->dispatch(new ProvideApiResponseForContractCommand($orders));
    }
}
