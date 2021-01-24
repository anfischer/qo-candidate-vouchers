<?php

namespace App\Models;

use App\Traits\ValidateTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OrderLine[] $lines
 * @property-read int|null $lines_count
 * @property-read \App\Voucher|null $voucher
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order query()
 * @method static \Illuminate\Database\Query\Builder|\App\Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Order withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $total
 * @property int $voucher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereVoucherId($value)
 */
class Order extends Model
{
    use ValidateTrait;

    protected $visible = [
        'id',
        'voucher_id',
        'total',
        'updated',
        'created'
    ];

    protected $fillable = [
        'voucher_id',
        'total'
    ];

    public function calculateTotal()
    {
        $total = 0;

        if ($this->lines && $this->lines->isNotEmpty()) {
            $total += $this->lines->sum(fn (OrderLine $ol) => $ol->amount_total);
        }

        if ($this->voucher) {
            $total += $this->voucher->amount_original;
        }

        $this->total = $total;
    }

    public function lines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public static function createRules()
    {
        return [
            'voucher_id'           => 'int|nullable',
        ];
    }

    public static function updateRules()
    {
        return [
            'voucher_id'           => 'int|nullable',
        ];
    }
}
