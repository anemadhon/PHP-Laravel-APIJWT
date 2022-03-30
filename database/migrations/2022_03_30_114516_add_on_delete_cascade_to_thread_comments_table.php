<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnDeleteCascadeToThreadCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thread_comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            $table->dropForeign(['thread_id']);
            $table->foreign('thread_id')
                    ->references('id')
                    ->on('threads')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thread_comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users');
            $table->dropForeign(['thread_id']);
            $table->foreign('thread_id')
                    ->references('id')
                    ->on('threads');
        });
    }
}
