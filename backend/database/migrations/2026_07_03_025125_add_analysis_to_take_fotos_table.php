<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('take_fotos', function (Blueprint $table) {
            // Menggunakan TEXT karena hasil analisis Gemini biasanya panjang (Markdown/Paragraf)
            $table->text('analysis')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('take_fotos', function (Blueprint $table) {
            $table->dropColumn('analysis');
        });
    }
};
