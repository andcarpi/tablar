<?php

use TakiElias\Tablar\Tablar;

class ServiceProviderTest extends TestCase
{
    public function testRegisterSingletonInstance()
    {
        // Check the instance of Tablar resolver.

        $tablar = $this->app->make(Tablar::class);
        $this->assertInstanceOf(Tablar::class, $tablar);

        // Check that a singleton instance is registered.

        $this->assertSame($tablar, $this->app->make(Tablar::class));
    }

    public function testBootLoadViews()
    {
        // Check that the main views are loaded.

        $this->assertTrue(View::exists('tablar::master'));
        $this->assertTrue(View::exists('tablar::page'));
        $this->assertTrue(View::exists('tablar::pagination'));
        $this->assertTrue(View::exists('tablar::auth.login'));
        $this->assertTrue(View::exists('tablar::auth.register'));
        $this->assertTrue(View::exists('tablar::auth.verify'));
        $this->assertTrue(View::exists('tablar::auth.passwords.email'));
        $this->assertTrue(View::exists('tablar::auth.passwords.reset'));
    }

    public function testBootLoadTranslations()
    {
        // Check that the main translations are loaded.

        $this->assertTrue(Lang::has('tablar::tablar.sign_in'));
        $this->assertTrue(Lang::has('tablar::menu.main_navigation'));
    }

    public function testBootLoadConfig()
    {
        // Check that config values are loaded.

        $this->assertTrue(Config::has('tablar.title'));
        $this->assertEquals('Tablar', Config::get('tablar.title'));

        $this->assertTrue(Config::has('tablar.menu'));
        $this->assertTrue(is_array(Config::get('tablar.menu')));
    }


    public function testLayout()
    {
        $this->assertTrue(Config::has('tablar.layout_class'));
        $this->assertTrue(in_array(Config::get('tablar.layout_class'), ['default', 'layout-fluid', 'layout-boxed']));
    }

    public function testBootRegisterCommands()
    {
        // Check that the artisan commands are registered.

        $commands = Artisan::all();
        $this->assertTrue(Arr::has($commands, 'tablar:install'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-all'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-js'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-config'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-auth'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-views'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-assets'));
    }

    public function testBootRegisterViewComposers()
    {
        // Check that the Tablar instance exists on the page blade.

        $view = View::make('tablar::page');
        View::callComposer($view);
        $viewData = $view->getData();

        $this->assertTrue(Arr::has($viewData, 'tablar'));
    }

    public function testBootRegisterMenu()
    {
        $tablar = $this->app->make(Tablar::class);
        $menu = $tablar->menu();

        $this->assertCount(2, $menu);
        $this->assertEquals('Home', $menu[0]['text']);
    }

    public function ttestBootLoadComponents()
    {
        // Check that some of the blade component views are loaded.

        $this->assertTrue(View::exists('adminlte::components.form.input'));
        $this->assertTrue(View::exists('adminlte::components.form.select2'));
        $this->assertTrue(View::exists('adminlte::components.widget.card'));
        $this->assertTrue(View::exists('adminlte::components.tool.modal'));

        // Support of x-components is only available for Laravel >= 7.x
        // versions. So, check if we can test for component existence first.

        $canCheckComponents = method_exists(
            'Illuminate\Support\Facade\Blade',
            'getClassComponentAliases'
        );

        if (!$canCheckComponents) {
            return;
        }

        // Now, check that the class components aliases are registered.

        $aliases = Blade::getClassComponentAliases();

        $this->assertTrue(isset($aliases['adminlte-input']));
        $this->assertTrue(isset($aliases['adminlte-select2']));
        $this->assertTrue(isset($aliases['adminlte-card']));
        $this->assertTrue(isset($aliases['adminlte-modal']));
    }

    public function ttestBootLoadRoutes()
    {
        // Assert the package routes names are registered.

        $this->assertTrue(Route::has('adminlte.darkmode.toggle'));
    }
}
