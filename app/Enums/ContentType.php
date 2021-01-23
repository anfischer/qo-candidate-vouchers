<?php

declare(strict_types=1);

namespace App\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ContentType JSON()
 * @psalm-immutable
 */
class ContentType extends Enum
{
    private const JSON = 'json';
}
