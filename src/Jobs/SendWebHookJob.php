<?php

namespace PsychoB\WebHook\Jobs;

use PsychoB\WebHook\Models\Payload;
use PsychoB\WebHook\Services\WebHookSenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWebHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Payload
     */
    private $payload;

    /**
     * SendWebHookJob constructor.
     * @param Payload $payload
     */
    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
    }

    public function handle(WebHookSenderService $service)
    {
        $e = $service->send($this->payload);

        if ($e) {
            $this->fail($e);
        }
    }

    public function tags()
    {
        return [
            'webhook',
            'payload:'.$this->payload->uuid,
        ];
    }
}
