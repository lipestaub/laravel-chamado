<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateChamadoTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chamado_temp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->string('empresa');
            $table->string('email');
            $table->string('telefone');
            $table->string('titulo');
            $table->text('mensagem');
            $table->string('anexo')->nullable();
            $table->string('chave_acesso');
            $table->integer('datahora');
            $table->string('ip');
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
        Schema::drop('chamado_temp');
    }
}
