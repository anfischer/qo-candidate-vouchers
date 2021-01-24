<?php

namespace App\Models;

use App\Collections\VoucherCollection;
use App\Traits\ValidateTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Voucher
 *
 * @property-read \App\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Voucher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher query()
 * @method static \Illuminate\Database\Query\Builder|\App\Voucher withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Voucher withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $amount_original
 * @property int $amount_remaining
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereAmountOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereAmountRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voucher whereUpdatedAt($value)
 */
class Voucher extends Model
{
    use ValidateTrait;

    protected $visible = [
        'id',
        'amount_original',
        'amount_remaining',
        'updated',
        'created'
    ];

    protected $fillable = [
        'amount_original',
        'amount_remaining'
    ];

    public static function createRules(): array
    {
        return [
            'amount_original'   => 'int',
            'amount_remaining'  => 'int',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'amount_original'   => 'int',
            'amount_remaining'  => 'int',
        ];
    }

    public function newCollection(array $models = []): VoucherCollection
    {
        return new VoucherCollection($models);
    }
}
