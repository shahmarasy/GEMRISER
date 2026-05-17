<?php

declare(strict_types=1);

namespace Gemriser\View;

use Illuminate\View\Factory;
use Illuminate\View\View;

class ViewFactory
{
    public function __construct(private Factory $factory)
    {
    }

    public function make(string $template, array $data = []): View
    {
        return $this->factory->make($template, $data);
    }

    public function share(string $key, mixed $value): void
    {
        $this->factory->share($key, $value);
    }

    public function exists(string $template): bool
    {
        return $this->factory->exists($template);
    }
}
