<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('sbd')->unique()->index()->comment('Số báo danh');
            
            $table->decimal('toan', 3, 1)->nullable()->comment('Toán');
            $table->decimal('ngu_van', 3, 1)->nullable()->comment('Ngữ Văn');
            $table->decimal('ngoai_ngu', 3, 1)->nullable()->comment('Ngoại Ngữ');
            $table->decimal('vat_li', 3, 1)->nullable()->comment('Vật Lý');
            $table->decimal('hoa_hoc', 3, 1)->nullable()->comment('Hóa Học');
            $table->decimal('sinh_hoc', 3, 1)->nullable()->comment('Sinh Học');
            $table->decimal('lich_su', 3, 1)->nullable()->comment('Lịch Sử');
            $table->decimal('dia_li', 3, 1)->nullable()->comment('Địa Lý');
            $table->decimal('gdcd', 3, 1)->nullable()->comment('GDCD');
            
            $table->string('ma_ngoai_ngu')->nullable()->comment('Mã ngoại ngữ');
            
            $table->timestamps();
            
            $table->index('toan');
            $table->index('vat_li');
            $table->index('hoa_hoc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
