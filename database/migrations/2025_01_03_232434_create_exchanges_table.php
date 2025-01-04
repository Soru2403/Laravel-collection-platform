<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();  // ID записи
            $table->foreignId('user_id_1')
                  ->constrained('users') 
                  ->onDelete('cascade');  
            $table->foreignId('user_id_2') 
                  ->constrained('users')  
                  ->onDelete('cascade'); 
            $table->foreignId('collection_id_1')
                  ->constrained('collections') 
                  ->onDelete('cascade');  
            $table->foreignId('collection_id_2') 
                  ->constrained('collections') 
                  ->onDelete('cascade');  
            $table->enum('status', ['pending', 'accepted', 'rejected']) 
                  ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchanges');
    }
}

