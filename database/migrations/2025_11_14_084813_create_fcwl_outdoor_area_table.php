<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCWL_Outdoor_Area
     * Purpose: Data area outdoor untuk wireless
     * Depends on: form_checklist_wireless
     */
    public function up(): void
    {
        Schema::create('fcwl_outdoor_area', function (Blueprint $table) {
            // Primary Key
            $table->id('id_outdoor');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_fcwl')->unique();

            // Base Station Information
            $table->string('bs_catuan_sektor', 100)->nullable();
            $table->enum('los_ke_bs_catuan', ['ya', 'tidak'])->nullable();
            $table->string('jarak_udara', 100)->nullable();
            $table->string('heading', 100)->nullable();

            // GPS Coordinates
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();

            // Obstacles
            $table->text('potential_obstacle')->nullable();

            // Mounting Information
            $table->string('type_mounting', 100)->nullable();
            $table->enum('mounting_tidak_goyang', ['ya', 'tidak'])->nullable();
            $table->string('center_of_gravity', 100)->nullable();

            // Lightning Protection
            $table->enum('disekitar_mounting_ada_penangkal_petir', ['ya', 'tidak'])->nullable();
            $table->string('sudut_mounting_terhadap_penangkal_petir', 100)->nullable();

            // Height & Protection Type
            $table->string('tinggi_mounting', 100)->nullable();
            $table->string('type_penangkal_petir', 100)->nullable();

            // Index
            $table->index('id_fcwl');

            // Foreign Key Constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
