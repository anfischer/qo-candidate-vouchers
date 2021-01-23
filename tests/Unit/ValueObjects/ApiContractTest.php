<?php
declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\Enums\ApiVersion;
use App\Enums\ContentType;
use App\ValueObjects\ApiContract;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ApiContractTest extends TestCase
{
    /** @test */
    public function it_can_get_its_api_version(): void
    {
        $apiContractForV1 = ApiContract::fromRequestAcceptHeader('application/vnd.quickorder.v1+json');
        $this->assertTrue(ApiVersion::V1()->equals($apiContractForV1->toVersionConstraint()));

        $apiContractForV2 = ApiContract::fromRequestAcceptHeader('application/vnd.quickorder.v2+json');
        $this->assertTrue(ApiVersion::V2()->equals($apiContractForV2->toVersionConstraint()));
    }

    /** @test */
    public function if_no_parsable_api_version_is_provided_it_will_default_to_api_v1(): void
    {
        $apiContractFromNonParsableHeader = ApiContract::fromRequestAcceptHeader('application/vnd.not-parsable');
        $this->assertTrue(ApiVersion::V1()->equals($apiContractFromNonParsableHeader->toVersionConstraint()));
    }

    /** @test */
    public function it_does_not_allow_api_versions_lower_than_v1(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ApiContract::fromRequestAcceptHeader('application/vnd.quickorder.v0+json');
    }

    /** @test */
    public function it_does_not_allow_api_versions_higher_than_v2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ApiContract::fromRequestAcceptHeader('application/vnd.quickorder.v3+json');
    }

    /** @test */
    public function it_can_get_its_content_type(): void
    {
        $apiContract = ApiContract::fromRequestAcceptHeader('application/vnd.quickorder.v1+json');
        $this->assertTrue(ContentType::JSON()->equals($apiContract->toContentType()));
    }
}
