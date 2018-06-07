# Laravel WebHook

## Setup

To get Laravel WebHook ready for use in your project, take the usual steps for setting up a Laravel package.

 1. Run: ```composer require psychob/laravel-webhook```
 2. Add to your service provider: ```PsychoB\WebHook\Providers\WebHookProvider::class```
 3. Run ```php artisan vendor:publish```
 4. Run ```php artisan migrate```
 5. Add to alias: ```
        'WebHook' => \PsychoB\WebHook\Facades\WebHook::class,
        ```

## Sending WebHook

```
\WebHook::push(method, url, array with data, additional headers);
```