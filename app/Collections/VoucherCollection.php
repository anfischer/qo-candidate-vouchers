<?php
declare(strict_types=1);

namespace App\Collections;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;

final class VoucherCollection extends Collection
{
    public function totalOriginalAmount(): int
    {
        return $this->sum(static function (Voucher $voucher) {
            return $voucher->amount_original;
        });
    }
}
