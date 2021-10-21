<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Conexionesftps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('conexionesftps', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('Host', 100);
            $table->bigInteger('Puerto');
            $table->string('Cifrado', 15);
            $table->string('User', 50);
            $table->string('Password', 100);
            $table->string('Ruta', 100);
            $table->boolean('Activo');
            
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
        //
    }
}
