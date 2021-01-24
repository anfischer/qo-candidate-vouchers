<?php
declare(strict_types=1);

namespace App\Collections;

use App\Enums\ApiVersion;
use App\CanTransform;
use App\ValueObjects\ApiContract;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;

final class OrderCollection extends Collection implements CanTransform
{
    public function transformerForApiContract(ApiContract $apiContract): TransformerAbstract
    {
        if ($apiContract->toVersionConstraint()->equals(ApiVersion::V2())) {
            return new \App\Http\Transformers\Order\V2\OrderTransformer();
        }

        return new \App\Http\Transformers\Order\V1\OrderTransformer();
    }
}
