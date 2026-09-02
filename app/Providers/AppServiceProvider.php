<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Events\CommentAdded;
use App\Events\TaskAssigned;
use App\Events\TaskCreated;
use App\Events\TaskMoved;
use App\Listeners\LogTaskActivity;
use Illuminate\Support\Facades\Event;
use App\Listeners\SendTaskAssignedNotification;
use App\Listeners\SendTaskCommentedNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Event::listen(TaskCreated::class, LogTaskActivity::class);
        Event::listen(TaskMoved::class, LogTaskActivity::class);
        Event::listen(TaskAssigned::class, LogTaskActivity::class);
        Event::listen(CommentAdded::class, LogTaskActivity::class);
        Event::listen(TaskAssigned::class, SendTaskAssignedNotification::class);
        Event::listen(CommentAdded::class, SendTaskCommentedNotification::class);

    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
