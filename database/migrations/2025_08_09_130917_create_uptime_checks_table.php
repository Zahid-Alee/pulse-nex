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
        Schema::create('uptime_checks', function (Blueprint $table) {
  $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->string('status'); // up, down
            $table->integer('response_time')->nullable(); // milliseconds
            $table->integer('status_code')->nullable(); // HTTP status code
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
            
            $table->index(['website_id', 'checked_at']);
            $table->index(['website_id', 'status', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uptime_checks');
    }
};
