<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('user_id')->index();
            $table->string('phone_number', 20)->after('customer_name');
            $table->text('address')->after('phone_number');
            $table->date('pickup_date')->nullable()->after('order_date');
            $table->string('payment_method', 50)->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'phone_number', 'address', 'pickup_date', 'payment_method']);
        });
    }
};
