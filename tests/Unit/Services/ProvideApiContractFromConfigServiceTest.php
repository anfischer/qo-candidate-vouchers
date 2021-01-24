<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ApiVersion;
use App\Enums\ContentType;
use App\Services\ProvideApiContractFromConfigService;
use Illuminate\Contracts\Config\Repository;
use Mockery;
use PHPUnit\Framework\TestCase;

class ProvideApiContractFromConfigServiceTest extends TestCase
{
    /** @test */
    public function it_provides_an_api_contract_based_on_configuration_values(): void
    {
        $configRepository = Mockery::mock(Repository::class);
        $configRepository->shouldReceive('get')->with('app.api_version')->once()->andReturn('v2');
        $configRepository->shouldReceive('get')->with('app.api_content_type')->once()->andReturn('json');

        $service = new ProvideApiContractFromConfigService($configRepository);
        $apiContract = $service->provideApplicationsApiContract();

        $this->assertTrue(ApiVersion::V2()->equals($apiContract->toVersionConstraint()));
        $this->assertTrue(ContentType::JSON()->equals($apiContract->toContentType()));
    }
}
