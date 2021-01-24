<?php

namespace App\Models;

use App\Traits\ValidateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\OrderLine
 *
 * @property-read \App\Order $order
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\OrderLine onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine query()
 * @method static \Illuminate\Database\Query\Builder|\App\OrderLine withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\OrderLine withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $description
 * @property int $product_id
 * @property int $amount_each
 * @property int $amount_total
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereAmountEach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereAmountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereUpdatedAt($value)
 * @property int $order_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OrderLine whereOrderId($value)
 */
class OrderLine extends Model
{
    use ValidateTrait;

    protected $visible = [
        'id',
        'order_id',
        'product_id',
        'description',
        'amount_each',
        'amount_total',
        'quantity',
        'updated',
        'created'
    ];

    protected $fillable = [
        'product_id',
        'description',
        'amount_each',
        'amount_total',
        'quantity'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function createRules(): array
    {
        return [
            'product_id'        => 'int|nullable',
            'amount_each'       => 'int|nullable',
            'amount_total'      => 'int|nullable',
            'quantity'          => 'int|nullable',
            'description'       => 'string|nullable',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'product_id'        => 'int|nullable',
            'amount_each'       => 'int|nullable',
            'amount_total'      => 'int|nullable',
            'quantity'          => 'int|nullable',
            'description'       => 'string|nullable',
        ];
    }
}
