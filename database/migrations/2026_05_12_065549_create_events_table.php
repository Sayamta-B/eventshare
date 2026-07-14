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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->nullable()->unique();

            $table->text('description')->nullable();

            $table->dateTime('event_date')->nullable();

            $table->string('location')->nullable();

            $table->dateTime('upload_deadline')
                ->nullable();

            $table->boolean('gallery_visible')
                ->default(true);

            $table->timestamp('last_upload_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
