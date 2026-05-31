<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hardwares', function (Blueprint $table) {
            $table->float('suhu')->nullable();
            $table->float('kelembapan_udara')->nullable();
            $table->float('kelembapan_tanah')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hardwares', function (Blueprint $table) {
            $table->dropColumn([
                'suhu',
                'kelembapan_udara',
                'kelembapan_tanah'
            ]);
        });
    }
};
