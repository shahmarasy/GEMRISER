<?php

use Illuminate\Database\Schema\Blueprint;

return new class extends \Gemriser\Database\Migration\Migration
{
    public function up(): void
    {
        $this->capsule->schema()->create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        $this->capsule->schema()->dropIfExists('sessions');
    }
};
