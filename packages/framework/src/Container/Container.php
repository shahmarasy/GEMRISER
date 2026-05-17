<?php

declare(strict_types=1);

namespace Gemriser\Container;

use Illuminate\Container\Container as IlluminateContainer;
use Psr\Container\ContainerInterface;

class Container extends IlluminateContainer implements ContainerInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->instance(self::class, $this);
        $this->instance(ContainerInterface::class, $this);
    }
}
