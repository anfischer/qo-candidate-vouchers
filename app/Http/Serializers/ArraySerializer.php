<?php

namespace App\Http\Serializers;

class ArraySerializer extends \League\Fractal\Serializer\ArraySerializer
{
    public function collection($resourceKey, array $data): array
    {
        return $data;
    }
}
