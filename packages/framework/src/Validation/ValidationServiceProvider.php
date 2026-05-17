<?php

declare(strict_types=1);

namespace Gemriser\Validation;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('validator', function (Application $app) {
            $translator = new Translator(new ArrayLoader(), $app->make('config')->get('app.locale', 'en'));
            return new Factory($translator);
        });
    }
}
