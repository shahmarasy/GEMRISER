<?php

declare(strict_types=1);

namespace Gemriser\Database\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration as IlluminateMigration;

abstract class Migration extends IlluminateMigration
{
    protected Capsule $capsule;

    public function __construct()
    {
        $this->capsule = Capsule::getInstance();
    }

    abstract public function up(): void;
    abstract public function down(): void;
}
