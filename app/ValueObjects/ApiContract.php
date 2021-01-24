<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\ApiVersion;
use App\Enums\ContentType;
use Webmozart\Assert\Assert;

final class ApiContract
{
    private ApiVersion $apiVersion;
    private ContentType $contentType;

    private function __construct(string $apiVersion, string $contentType)
    {
        Assert::inArray($apiVersion, [
            ApiVersion::V1()->getValue(),
            ApiVersion::V2()->getValue(),
        ]);

        $this->apiVersion = new ApiVersion($apiVersion);
        $this->contentType = new ContentType($contentType);
    }

    public static function fromRequestAcceptHeader(string $header): self
    {
        // The application accepts "Accept headers" in the format of "application/vnd.quickorder.v[CONSTRAINT]+[TYPE]".
        // To keep this proof of concept version simple, only CONSTRAINT as v2 and v1 is accepted for API version.
        // Also only content negotiation in the form of JSON is accepted.
        $matches = [];
        preg_match('/^application\/vnd\.quickorder\.(v[\d])\+json$/', $header, $matches);

        if (count($matches) === 0) {
            return new self(ApiVersion::V1()->getValue(), 'json');
        }

        return new self($matches[1], 'json');
    }

    public static function fromConfigValues(string $apiVersion, string $contentType): self
    {
        return new self($apiVersion, $contentType);
    }

    public function toVersionConstraint(): ApiVersion
    {
        return $this->apiVersion;
    }

    public function toContentType(): ContentType
    {
        return $this->contentType;
    }
}
