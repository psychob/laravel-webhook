<?php

namespace PsychoB\WebHook\Events;

use PsychoB\WebHook\Models\Payload;
use PsychoB\WebHook\Models\Request;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FailedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Payload
     */
    private $payload;

    /**
     * @var Request
     */
    private $request;

    /**
     * FailedEvent constructor.
     * @param Payload $payload
     * @param Request $request
     */
    public function __construct(Payload $payload, Request $request)
    {
        $this->payload = $payload;
        $this->request = $request;
    }

    /**
     * @return Payload
     */
    public function getPayload(): Payload
    {
        return $this->payload;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}