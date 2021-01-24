<?php
declare(strict_types=1);

namespace App\Commands;

use App\CanTransform;
use App\ValueObjects\ApiContract;
use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\TransformerAbstract;
use Traversable;
use Webmozart\Assert\Assert;

final class ProvideApiResponseForContractCommand
{
    private CanTransform $data;

    public function __construct(CanTransform $data)
    {
        Assert::isInstanceOfAny($data, [Traversable::class, Arrayable::class]);

        $this->data = $data;
    }

    public function getData(): CanTransform
    {
        return $this->data;
    }

    public function getTransformer(ApiContract $apiContract): TransformerAbstract
    {
        return $this->data->transformerForApiContract($apiContract);
    }

    public function shouldTransformToCollection(): bool
    {
        return $this->data instanceof Traversable;
    }
}
