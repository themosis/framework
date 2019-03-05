<?php

namespace Themosis\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Filesystem\Filesystem;

class AuthMakeCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The console command name and signature.
     *
     * @var string
     */
    protected $signature = 'make:auth
                    {--views : Only scaffold the authentication views}s
                    {--force : Overwrite existing files by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold default authentication files';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Authentication views.
     *
     * @var array
     */
    protected $views = [
        'auth/login.stub' => 'auth/login.blade.php',
        'auth/register.stub' => 'auth/register.blade.php',
        'auth/verify.stub' => 'auth/verify.blade.php',
        'auth/passwords/email.stub' => 'auth/passwords/email.blade.php',
        'auth/passwords/reset.stub' => 'auth/passwords/reset.blade.php',
        'settings/home.stub' => 'settings/home.blade.php'
    ];

    /**
     * Authentication controllers.
     *
     * @var array
     */
    protected $controllers = [
        'Auth/ForgotPasswordController.stub' => 'Auth/ForgotPasswordController.php',
        'Auth/LoginController.stub' => 'Auth/LoginController.php',
        'Auth/RegisterController.stub' => 'Auth/RegisterController.php',
        'Auth/ResetPasswordController.stub' => 'Auth/ResetPasswordController.php',
        'Auth/VerificationController.stub' => 'Auth/VerificationController.php',
        'SettingsController.stub' => 'SettingsController.php'
    ];

    /**
     * Authentication forms.
     *
     * @var array
     */
    protected $forms = [
        'Auth/Passwords/EmailResetForm.stub' => 'Auth/Passwords/EmailResetForm.php',
        'Auth/Passwords/PasswordResetForm.stub' => 'Auth/Passwords/PasswordResetForm.php',
        'Auth/LoginForm.stub' => 'Auth/LoginForm.php',
        'Auth/LogoutForm.stub' => 'Auth/LogoutForm.php',
        'Auth/RegisterForm.stub' => 'Auth/RegisterForm.php'
    ];

    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        if (! $this->option('views')) {
            $this->exportControllers();
            $this->exportForms();
            $this->files->append(
                base_path('routes/web.php'),
                $this->files->get(__DIR__.'/stubs/make/routes.stub')
            );
        }

        $this->info('Authentication scaffolding generated successfully.');
    }

    /**
     * Create auth necessary directories.
     */
    protected function createDirectories()
    {
        if (! $this->files->isDirectory($directory = app_path('Forms/Auth/Passwords'))) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if (! $this->files->isDirectory($directory = app_path('Http/Controllers/Auth'))) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if (! $this->files->isDirectory($directory = resource_path('views/auth/passwords'))) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if (! $this->files->isDirectory($directory = resource_path('views/settings'))) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Export authentication default views.
     */
    protected function exportViews()
    {
        foreach ($this->views as $pathIn => $pathOut) {
            if ($this->files->exists($view = resource_path('views/'.$pathOut)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$pathOut}] view already exists. Do you want to overwrite it?")) {
                    continue;
                }
            }

            $this->files->copy(
                __DIR__.'/stubs/make/views/'.$pathIn,
                $view
            );
        }
    }

    /**
     * Export authentication default controllers.
     */
    protected function exportControllers()
    {
        foreach ($this->controllers as $pathIn => $pathOut) {
            if ($this->files->exists($controller = app_path('Http/Controllers/'.$pathOut))
                && ! $this->option('force')) {
                if (! $this->confirm("The [{$pathOut}] controller already exists. Do you want to overwrite it?")) {
                    continue;
                }
            }

            $this->files->put(
                $controller,
                $this->compileStub($this->files->get(__DIR__.'/stubs/make/controllers/'.$pathIn))
            );
        }
    }

    /**
     * Export authentication default forms.
     */
    protected function exportForms()
    {
        foreach ($this->forms as $pathIn => $pathOut) {
            if ($this->files->exists($form = app_path('Forms/'.$pathOut)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$pathOut}] form already exists. Do you want to overwrite it?")) {
                    continue;
                }
            }

            $this->files->put(
                $form,
                $this->compileStub($this->files->get(__DIR__.'/stubs/make/forms/'.$pathIn))
            );
        }
    }

    /**
     * Compile controller content.
     *
     * @param string $content
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    protected function compileStub(string $content)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            $content
        );
    }
}
