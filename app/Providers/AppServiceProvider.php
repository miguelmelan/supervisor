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
                    'dateFormat' => $language['dateFormat'],
                    'timeFormat' => $language['timeFormat'],
                ];
            },
            'translations' => function () {
                return translations(
                    resource_path('../lang/' . app()->getLocale() . '.json')
                );
            },
        ]);

        // Inside of the boot() method.
        HasMany::macro('createUpdateOrDelete', function (iterable $records) {
            /** @var HasMany */
            $hasMany = $this;

            return (new CreateUpdateOrDelete($hasMany, $records))();
        });
    }
}
