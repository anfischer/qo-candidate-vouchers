<?php

namespace App\Http\Requests;

use App\Enums\ApiVersion;
use App\Services\ProvidesApiContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Exists;

class CreateOrderRequest extends FormRequest
{
    public function rules(ProvidesApiContract $contractProvider): array
    {
        $contract = $contractProvider->provideApplicationsApiContract();

        if ($contract->toVersionConstraint()->equals(ApiVersion::V2())) {
            return $this->rulesForApiVersionTwo();
        }

        return $this->rulesForApiVersionOne();
    }

    public function vouchers(): Collection
    {
        return Collection::wrap(
            $this->get('voucher_id', null) ?? $this->get('voucher_ids')
        );
    }

    private function rulesForApiVersionOne(): array
    {
        return [
            'voucher_id' => 'int|nullable',
        ];
    }

    private function rulesForApiVersionTwo(): array
    {
        return [
            'voucher_ids' => 'array|nullable',
            'voucher_ids.*' => ['int', new Exists('vouchers', 'id')],
        ];
    }
}
