<?php
declare(strict_types=1);

namespace App\Services;

use App\Commands\ProvideApiResponseForContractCommand;
use App\Http\Serializers\ArraySerializer;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

final class ApiResponseProviderService
{
    private ProvidesApiContract $apiContractResolver;
    private ResponseFactory $responseFactory;
    private Manager $manager;

    public function __construct(
        ProvidesApiContract $apiContractResolver,
        ResponseFactory $responseFactory,
        Manager $manager
    ) {
        $this->apiContractResolver = $apiContractResolver;
        $this->responseFactory = $responseFactory;
        $this->manager = $manager;

        $this->manager->setSerializer(new ArraySerializer());
    }

    public function handle(ProvideApiResponseForContractCommand $command): JsonResponse
    {
        $transformer = $command->getTransformer(
            $this->apiContractResolver->provideApplicationsApiContract(),
        );

        if ($command->shouldTransformToCollection()) {
            $resource = new Collection($command->getData(), $transformer);
        } else {
            $resource = new Item($command->getData(), $transformer);
        }

        return $this->responseFactory->json(
            $this->manager->createData($resource)->toArray()
        );
    }
}
