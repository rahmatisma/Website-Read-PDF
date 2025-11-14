<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fcwl_outdoor_area', function (Blueprint $table) {
            $table->id('id_outdoor');
            $table->unsignedBigInteger('id_fcwl')->unique();
            $table->string('bs_catuan_sektor', 100)->nullable();
            $table->string('los_ke_bs_catuan', 100)->nullable();
            $table->string('jarak_udara', 100)->nullable();
            $table->string('heading', 100)->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->text('potential_obstacle')->nullable();
            $table->string('type_mounting', 100)->nullable();
            $table->string('mounting_tidak_goyang', 100)->nullable();
            $table->string('center_of_gravity', 100)->nullable();
            $table->string('disekitar_mounting_ada_penangkal_petir', 100)->nullable();
            $table->string('sudut_mounting_terhadap_penangkal_petir', 100)->nullable();
            $table->string('tinggi_mounting', 100)->nullable();
            $table->string('type_penangkal_petir', 100)->nullable();

            // Foreign key constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade');

            // Index
            $table->index('id_fcwl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcwl_outdoor_area');
    }
};
