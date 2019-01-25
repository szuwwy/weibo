<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //我们发现 diffForHumans 为我们生成的时间是英文的，如果要使用中文时间，则需要对 Carbon 进行本地化设置。Carbon 是 PHP DateTime 的一个简单扩展，Laravel 将其默认集成到了框架中。对 Carbon 进行本地化的设置很简单，只在 AppServiceProvider 中调用 Carbon 的 setLocale 方法即可
        Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
