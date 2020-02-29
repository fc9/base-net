<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeUnilevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $status = improve(config('net.node.status'));
        $bonus = improve(config('net.bonus.status'));

        Schema::create('node_unilevel', function (Blueprint $table) use ($status, $bonus) {
            $table->unsignedBigInteger('node_id')->index()->unique();
            $table->unsignedBigInteger('parent_node_id')->index()->nullable();
            $table->unsignedBigInteger('indication_order')->autoIncrement()->unique();
            $table->string('lineage', 10000)->nullable(); # 2/45/465...
            $table->enum('node_status', $status->values())->default($status->first());
            $table->enum('bonus_status', $bonus->values())->default($bonus->first());
            $table->timestamps();

            $table->foreign('node_id')->references('id')->on('node');
            $table->foreign('parent_node_id')->references('node_id')->on('node_unilevel');

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_unilevel');
    }
}
