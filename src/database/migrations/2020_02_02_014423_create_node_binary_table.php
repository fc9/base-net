<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeBinaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $binary = config('net.binary');
        $fixed_leg = improve($binary['fixed_leg']);
        $work_leg = improve($binary['work_leg']);
        $status = improve(config('net.node.status'));
        unset($binary);

        Schema::create('node_binary', function (Blueprint $table) use ($fixed_leg, $work_leg, $status) {
            $table->unsignedBigInteger('node_id')->index()->unique();
            $table->unsignedBigInteger('parent_node_id')->index()->nullable();
            $table->unsignedBigInteger('left_child_user_id')->nullable(); #TODO: tmp
            $table->unsignedBigInteger('right_child_user_id')->nullable(); #TODO: tmp
            $table->string('lineage', 10000)->nullable(); # 2/15/132...
            $table->enum('fixed_leg', $fixed_leg->values())->default($fixed_leg->first());
            $table->enum('work_leg', $work_leg->values())->default($work_leg->first());
            $table->enum('status', $status->values())->default($status->first());
            $table->integer('left_pv')->default(0); #TODO: tmp
            $table->integer('right_pv')->default(0); #TODO: tmp
            $table->integer('left_count')->default(0);
            $table->integer('right_count')->default(0);
            $table->timestamps();

            $table->foreign('node_id')->references('id')->on('node');
            $table->foreign('parent_node_id')->references('node_id')->on('node_binary');

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
        Schema::dropIfExists('node_binary');
    }
}
