<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // bigint autoincrement (mejor que increments)
            $table->unsignedBigInteger('user_id'); // referencia externa

            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending');

            $table->decimal('total', 10, 2);

            $table->json('items')->nullable(); // mejor que text para pedidos
            $table->string('shipping_address')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            // $table->softDeletes(); // solo si luego activas SoftDeletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
