<?php
declare(strict_types=1);

namespace App\Services;

use App\ValueObjects\ApiContract;

interface ProvidesApiContract
{
    public function provideApplicationsApiContract(): ApiContract;
}
