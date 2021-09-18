<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatDetailPermintaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dat_detail_permintaan', function (Blueprint $table) {
            $table->string('id',36)->primary();
            $table->string('id_permintaan',36);
            $table->string('id_barang',36);
            $table->bigInteger('kuantiti',false);
            $table->text('keterangan')->nullable();
            $table->string('status',2)->nullable();
            $table->string('created_by',36)->nullable();
            $table->string('updated_by',36)->nullable();
            $table->string('deleted_by',36)->nullable();
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('dat_detail_permintaan');
    }
}
