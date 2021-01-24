<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderVoucherTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_voucher', function (Blueprint $table) {
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('voucher_id');

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('voucher_id')->references('id')->on('vouchers');
        });

        // A later deployment should migrate changes which drops the orders voucher_id column as it is not needed in the future.
        //
        // Before the column is dropped necessary steps must be taken to migrate all the existing (pre this release)
        // data in the orders column for voucher_ids, i.e. a simplified migration query could be something like the below query.
        // However, as the query is table locking for the full insert duration, large table sets will incur a lock for a long(er) period of time,
        // and a better approach to populating the pivot table with the existing data, will be to create a batch processing
        // feature which will chunk load old data in batches to not incur long locks. This could easily be provided via
        // a scheduled task dispatched every x-duration until data is up to sync. The scheduled task can then be remove
        // and the orders voucher_id column can be dropped.
        /*
        <<<SQL
            INSERT INTO
                order_voucher (order_id, voucher_id)
            SELECT
                o.id, o.voucher_id
            FROM
                orders o
            WHERE
                o.voucher_id IS NOT NULL;
        SQL
        */
    }

    public function down(): void
    {
    }
}
