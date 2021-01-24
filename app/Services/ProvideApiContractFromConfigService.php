<?php
declare(strict_types=1);

namespace App\Services;

use App\ValueObjects\ApiContract;
use Illuminate\Contracts\Config\Repository;

final class ProvideApiContractFromConfigService implements ProvidesApiContract
{
    private Repository $configRepository;

    public function __construct(Repository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function provideApplicationsApiContract(): ApiContract
    {
        return ApiContract::fromConfigValues(
            (string) $this->configRepository->get('app.api_version'),
            (string) $this->configRepository->get('app.api_content_type'),
        );
    }
}
