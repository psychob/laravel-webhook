<?php

namespace PsychoB\WebHook\Services;

use PsychoB\WebHook\Jobs\SendWebHookJob;
use PsychoB\WebHook\Models\Payload;
use PsychoB\WebHook\Testing\FakeWebHookService;

class WebHookService
{
    public const DEFAULT_USER_AGENT = 'psychob/laravel-webhook php/' . PHP_VERSION;

    /**
     * @param string $method
     * @param string $url
     * @param $body
     * @param string $userAgent
     * @param array $headers
     *
     * @return string
     */
    public function push(
        string $method,
        string $url,
        $body,
        string $userAgent = self::DEFAULT_USER_AGENT,
        array $headers = []
    ): string {
        $payload = new Payload([
            'status' => Payload::STATUS_INITIALIZED,
            'data' => $body,
            'request_headers' => $headers,
            'request_method' => $method,
            'request_url' => $url,
            'user_agent' => $userAgent,
        ]);

        $payload->save();

        \Log::debug('Creating new payload: '.$method.' '.$url.' UUID: '.$payload->uuid);

        SendWebHookJob::dispatch($payload)->onQueue(config('webhook.queue_name', 'webhook'));

        return $payload->uuid;
    }

    public function verify(string $hmac, string $response, ?string $secret = null): bool
    {
        if ($secret === null) {
            $secret = config('webhook.secret');
        }

        return hash_hmac('sha512', $response, $secret) === $hmac;
    }
}
