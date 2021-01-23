<?php

declare(strict_types=1);

namespace App\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ApiVersion V1()
 * @method static ApiVersion V2()
 * @psalm-immutable
 */
class ApiVersion extends Enum
{
    private const V1 = 'v1';
    private const V2 = 'v2';
}
