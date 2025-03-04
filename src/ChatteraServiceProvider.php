<?php

namespace Noodleware\Chattera;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Noodleware\Chattera\Console\Commands\AddChatteraContext;
use Noodleware\Chattera\Console\Commands\SendChatteraReport;
use Noodleware\Chattera\Livewire\Chatbot;
use Noodleware\Chattera\Services\OpenAIService;

class ChatteraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/chattera.php', 'chattera');

        $this->app->singleton(OpenAIService::class, function () {
            return new OpenAIService(
                config('chattera.openai.base_url', ''),
                config('chattera.openai.access_key', '')
            );
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'chattera');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AddChatteraContext::class,
                SendChatteraReport::class,
            ]);
        }

        Livewire::component('chatbot', Chatbot::class);

        $this->publishes([
            __DIR__.'/../config/chattera.php' => config_path('chattera.php'),
            __DIR__.'/../dist' => public_path('vendor/chattera'),
        ], 'chattera');
    }
}
