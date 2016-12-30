<?php

namespace jdavidbakr\MailTracker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MailTrackerServiceProvider extends ServiceProvider
{
    /**
     * Check to see if we're using lumen or laravel.
     *
     * @return bool
     */
    public function isLumen()
    {
        $lumenClass = 'Laravel\Lumen\Application';
        return ($this->app instanceof $lumenClass);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish pieces
        if (!$this->isLumen()) {
            $this->publishes([
                __DIR__.'/../config/mail-tracker.php' => config_path('mail-tracker.php')
            ], 'config');
            $this->publishes([
                __DIR__.'/../migrations/2016_03_01_193027_create_sent_emails_table.php' => database_path('migrations/2016_03_01_193027_create_sent_emails_table.php')
            ], 'config');
            $this->publishes([
                __DIR__.'/../migrations/2016_09_07_193027_create_sent_emails_Url_Clicked_table.php' => database_path('migrations/2016_09_07_193027_create_sent_emails_Url_Clicked_table.php')
            ], 'config');
            $this->publishes([
                __DIR__.'/../migrations/2016_12_30_100710_add_template_fields.php' => database_path('migrations/2016_12_30_100710_add_template_fields.php')
            ], 'config');
            $this->loadViewsFrom(__DIR__.'/views', 'emailTrakingViews');
            $this->publishes([
                __DIR__.'/views' => base_path('resources/views/vendor/emailTrakingViews'),
                ]);
        }

        // Hook into the mailer
        $this->app['mailer']->getSwiftMailer()->registerPlugin(new MailTracker());

        // Install the routes
        $config = $this->app['config']->get('mail-tracker.route', []);
        $config['namespace'] = 'jdavidbakr\MailTracker';

        if (!$this->isLumen()) {
            Route::group($config, function()
            {
                Route::get('t/{hash}', 'MailTrackerController@getT')->name('mailTracker_t');
                Route::get('l/{url}/{hash}', 'MailTrackerController@getL')->name('mailTracker_l');
            });
        } else {
            $app = $this->app;
            $app->group($config, function () use ($app) {
                $app->get('t', 'MailTrackerController@getT')->name('mailTracker_t');
                $app->get('l', 'MailTrackerController@getL')->name('mailTracker_l');
            });
        }
        // Install the Admin routes
        $config_admin = $this->app['config']->get('mail-tracker.admin-route', []);
        $config_admin['namespace'] = 'jdavidbakr\MailTracker';

        if (!$this->isLumen()) {
            Route::group($config_admin, function()
            {
                Route::get('/', 'AdminController@getIndex')->name('mailTracker_Index');
                Route::post('search', 'AdminController@postSearch')->name('mailTracker_Search');
                Route::get('clear-search', 'AdminController@clearSearch')->name('mailTracker_ClearSearch');
                Route::get('show-email/{id}', 'AdminController@getShowEmail')->name('mailTracker_ShowEmail');
                Route::get('url-detail/{id}', 'AdminController@getUrlDetail')->name('mailTracker_UrlDetail');
                Route::get('send-email', 'AdminController@getSendEmail')->name('mailTracker_SendEmail');
            });
        } else {
            $app = $this->app;
            $app->group($config_admin, function () use ($app) {
                $app->get('/', 'AdminController@getIndex')->name('mailTracker_Index');
                $app->post('search', 'AdminController@postSearch')->name('mailTracker_Search');
                $app->get('clear-search', 'AdminController@clearSearch')->name('mailTracker_ClearSearch');
                $app->get('show-email/{id}', 'AdminController@getShowEmail')->name('mailTracker_ShowEmail');
                $app->get('url-detail/{id}', 'AdminController@getUrlDetail')->name('mailTracker_UrlDetail');
                $app->get('send-email', 'AdminController@getSendEmail')->name('mailTracker_SendEmail');
            });
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
