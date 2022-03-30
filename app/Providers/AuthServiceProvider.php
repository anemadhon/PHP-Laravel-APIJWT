<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Thread;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('modify-comments', function(User $user, $comment)
        {
            return (int)$user->id === (int)$comment->user_id; 
        });
        
        Gate::define('modify-threads', function(User $user, Thread $thread)
        {
            return (int)$user->id === (int)$thread->user_id; 
        });
    }
}
