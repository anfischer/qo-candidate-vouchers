<?php
declare(strict_types=1);

namespace App;

use App\ValueObjects\ApiContract;
use League\Fractal\TransformerAbstract;

interface CanTransform
{
    public function transformerForApiContract(ApiContract $apiContract): TransformerAbstract;
}
