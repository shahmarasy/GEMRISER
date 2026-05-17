<?php

use Illuminate\Database\Schema\Blueprint;

return new class extends \Gemriser\Database\Migration\Migration
{
    public function up(): void
    {
        $this->capsule->schema()->create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        $this->capsule->schema()->dropIfExists('password_reset_tokens');
    }
};
