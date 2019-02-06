<?php
//
// laravel-webhook
// (c) 2019 Look4App <https://l4a-soft.com>
// (c) 2019 Andrzej Budzanowski <andrzej.budzanowski@l4a-soft.com>
//

namespace PsychoB\WebHook\Testing;

use PHPUnit\Framework\Assert as PHPUnit;
use PsychoB\WebHook\Models\Payload;

class FakeWebHookService
{
    public const DEFAULT_USER_AGENT = 'psychob/laravel-fake-webhook php/' . PHP_VERSION;

    protected $payloads = [];

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

        $this->payloads[] = $payload;

        return $payload->uuid;
    }

    public function verify(string $hmac, string $response, ?string $secret = null): bool
    {
        if ($secret === null) {
            $secret = config('webhook.secret');
        }

        return hash_hmac('sha512', $response, $secret) === $hmac;
    }

    public function assertWebHookCountSend(int $count)
    {
        PHPUnit::assertEquals($count, count($this->payloads));
    }

    public function assertWebHookSendForAddress(string $method, string $url)
    {
        PHPUnit::assertArraySubset([
            'method' => $method,
            'url' => $url,
        ], collect($this->payloads)->map(function (Payload $payload) {
            return [
                'method' => $payload->request_method,
                'url' => $payload->request_url,
            ];
        })->toArray());
    }

    public function assertWebHook(callable $callable)
    {
        collect($this->payloads)->map($callable);
    }
}
