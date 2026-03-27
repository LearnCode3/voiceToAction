<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // RPT-XXXX
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null = guest
            $table->string('submitter_name')->nullable();
            $table->string('submitter_email')->nullable();
            $table->string('category');              // slug — matches offices.slug
            $table->string('office_id');             // slug FK to offices.slug
            $table->string('office_name');           // denormalised for display
            $table->string('location');
            $table->text('description');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->enum('status', ['Submitted', 'Assigned', 'In Progress', 'Resolved'])->default('Assigned');
            $table->text('feedback')->nullable();
            $table->tinyInteger('feedback_rating')->nullable(); // 1-5
            $table->timestamps();
        });

        Schema::create('report_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_timelines');
        Schema::dropIfExists('reports');
    }
};
