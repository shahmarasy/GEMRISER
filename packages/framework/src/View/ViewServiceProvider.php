<?php

declare(strict_types=1);

namespace Gemriser\View;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('view', function (Application $app) {
            $viewPaths = $app->make('config')->get('view.paths', [$app->basePath('resources/views')]);
            $compiledPath = $app->make('config')->get('view.compiled', $app->storagePath('framework/views'));
            $filesystem = new Filesystem();
            $finder = new FileViewFinder($filesystem, $viewPaths);

            $resolver = new EngineResolver();
            $resolver->register('blade', function () use ($filesystem, $compiledPath) {
                $compiler = new BladeCompiler($filesystem, $compiledPath);
                return new CompilerEngine($compiler);
            });

            $factory = new Factory($resolver, $finder, new Dispatcher());
            $this->registerBladeDirectives($factory);

            return $factory;
        });
    }

    private function registerBladeDirectives(Factory $factory): void
    {
        $blade = $factory->getEngineResolver()->resolve('blade');

        if ($blade instanceof CompilerEngine) {
            $compiler = $blade->getCompiler();
            $compiler->directive('csrf', function () {
                return '<?php echo csrf_field(); ?>';
            });
            $compiler->directive('method', function (string $expression) {
                return "<?php echo method_field({$expression}); ?>";
            });
        }
    }
}
