<?php

namespace App\Models;

use App\CanTransform;
use App\Collections\OrderCollection;
use App\Enums\ApiVersion;
use App\ValueObjects\ApiContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\Fractal\TransformerAbstract;

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
class Order extends Model implements CanTransform
{
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

    public function newCollection(array $models = []): OrderCollection
    {
        return new OrderCollection($models);
    }

    public function transformerForApiContract(ApiContract $apiContract): TransformerAbstract
    {
        if ($apiContract->toVersionConstraint()->equals(ApiVersion::V2())) {
            return new \App\Http\Transformers\Order\V2\OrderTransformer();
        }

        return new \App\Http\Transformers\Order\V1\OrderTransformer();
    }

    public function calculateTotal(): void
    {
        $total = 0;

        if ($this->lines && $this->lines->isNotEmpty()) {
            $total += $this->lines->sum(fn (OrderLine $ol) => $ol->amount_total);
        }

        if ($this->voucher) {
            $total += $this->voucher->amount_original;
        } else {
            $total += $this->vouchers->reduce(static function (int $carry, Voucher $voucher) {
                $carry += $voucher->amount_original;

                return $carry;
            }, 0);
        }

        $this->total = $total;
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class);
    }
}
