<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Support\Macros\CreateUpdateOrDelete;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Inertia::share([
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'flash' => function () {
                return [
                    'message' => Session::get('message'),
                    'toast' => Session::get('toast'),
                    'toastStyle' => Session::get('toastStyle'),
                    'toastId' => Session::get('toastId'),
                ];
            },
            'locale' => function () {
                $locale = app()->getLocale();
                $language = config("languages.{$locale}");
                return [
                    'code' => $locale,
                    'lang' => $language['lang'],
                    'flag' => $language['flag'],
                    'dateFormats' => $language['dateFormats'],
                    'timeFormats' => $language['timeFormats'],
                ];
            },
            'translations' => function () {
                return translations(
                    resource_path('../lang/' . app()->getLocale() . '.json')
                );
            },
            'auth' => [
                'uipath' => env('AUTH_UIPATH_ENABLED'),
                'github' => env('AUTH_GITHUB_ENABLED'),
                'google' => env('AUTH_GOOGLE_ENABLED'),
                'microsoft' => env('AUTH_MICROSOFT_ENABLED'),
            ],
        ]);

        // Inside of the boot() method.
        HasMany::macro('createUpdateOrDelete', function (iterable $records) {
            /** @var HasMany */
            $hasMany = $this;

            return (new CreateUpdateOrDelete($hasMany, $records))();
        });
    }
}
