<?php

namespace PsychoB\WebHook\Services;

use PsychoB\WebHook\Events\FailedEvent;
use PsychoB\WebHook\Events\RetryEvent;
use PsychoB\WebHook\Events\SuccessEvent;
use PsychoB\WebHook\Jobs\SendWebHookJob;
use PsychoB\WebHook\Models\Payload as WebPayload;
use PsychoB\WebHook\Models\Request as WebRequest;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;

class WebHookSenderService
{
    public function send(WebPayload $payload)
    {
        $requestUuid = UuidService::random();
        $data = [
            'payload_uuid' => $payload->uuid,
            'request_uuid' => $requestUuid,
            'requested_at' => $payload->toArray()['created_at'],
            'data' => $payload->data,
        ];

        $encoded = json_encode($data, JSON_BIGINT_AS_STRING);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \RuntimeException("Can't convert array to json");
        }

        $client = new GuzzleClient();
        $webRequest = new Request($payload->request_method, $payload->request_url, array_merge([
            'Content-Type' => 'application/json',
            'User-Agent' => $payload->user_agent,
            'X-WebHook-Integrity' => $this->calculateIntegrityOf($encoded),
        ], $payload->request_headers), $encoded);

        $webHookRequest = new WebRequest([
            'payload_uuid' => $payload->uuid,
            'uuid' => $requestUuid,
            'request_url' => $payload->request_url,
            'request_headers' => $this->extractRequestHeaders($webRequest),
            'request_body' => $encoded,
        ]);

        $webResponse = $this->sendRequest($client, $webRequest, $webHookRequest);

        if ($webResponse !== null) {
            $this->handleCorrectRequest($payload, $webResponse, $webHookRequest);
        } else {
            $payload->status = WebPayload::STATUS_RETRY;
        }

        if ($payload->status === WebPayload::STATUS_RETRY) {
            $this->handleRetryRequest($payload, $webHookRequest);
        }

        $webHookRequest->save();
        $payload->save();
    }

    private function calculateIntegrityOf(string $data): string
    {
        return hash_hmac('sha512', $data, config('webhook.secret'));
    }

    /**
     * @param \Exception $e
     * @param WebRequest $webHookRequest
     */
    private function reportNotResponseError(\Exception $e, WebRequest $webHookRequest): void
    {
        $webHookRequest->response_status = -intval($e->getCode());
        $webHookRequest->response_headers = implode(PHP_EOL, [
            get_class($e),
            $e->getMessage(),
        ]);
        $webHookRequest->response_body = $e->getTraceAsString();
    }

    /**
     * @param WebPayload $payload
     */
    private function handleRetryRequest(WebPayload $payload, WebRequest $request): void
    {
        $count = WebRequest::where('payload_uuid', $payload->uuid)->count();

        if ($count >= config('webhook.retry')) {
            $payload->status = WebPayload::STATUS_FAILED;

            $this->reportFailed($payload, $request);
        } else {
            SendWebHookJob::dispatch($payload)->onQueue(config('webhook.queue_name'))->delay(config('webhook.retry_sleep'));

            $this->reportRetry($payload, $request);
        }
    }

    /**
     * @param $client
     * @param $webRequest
     * @param $webHookRequest
     *
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    private function sendRequest(GuzzleClient $client, Request $webRequest, WebRequest $webHookRequest)
    {
        $webResponse = null;

        try {
            $webResponse = $client->send($webRequest);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $webResponse = $e->getResponse();
            } else {
                $this->reportNotResponseError($e, $webHookRequest);
            }
        } catch (GuzzleException $e) {
            $this->reportNotResponseError($e, $webHookRequest);
        }

        return $webResponse;
    }

    /**
     * @param WebPayload $payload
     * @param $webResponse
     * @param $webHookRequest
     */
    private function handleCorrectRequest(
        WebPayload $payload,
        ResponseInterface $webResponse,
        WebRequest $webHookRequest
    ): void {
        $webHookRequest->response_status = $webResponse->getStatusCode();
        $webHookRequest->response_headers = $this->extractRequestHeaders($webResponse);
        $webHookRequest->response_body = $webResponse->getBody()->getContents();

        if ($webResponse->getStatusCode() === Response::HTTP_OK) {
            $payload->status = WebPayload::STATUS_OK;

            $this->reportSuccess($payload, $webHookRequest);
        } else {
            $payload->status = WebPayload::STATUS_RETRY;
        }
    }

    private function reportRetry(WebPayload $payload, WebRequest $request)
    {
        RetryEvent::dispatch($payload, $request);
    }

    private function reportSuccess(WebPayload $payload, WebRequest $request)
    {
        SuccessEvent::dispatch($payload, $request);
    }

    private function reportFailed(WebPayload $payload, WebRequest $request)
    {
        FailedEvent::dispatch($payload, $request);
    }

    /**
     * @param $webRequest
     * @return string
     */
    private function extractRequestHeaders($webRequest): string
    {
        $headers = $webRequest->getHeaders();
        array_walk($headers, function (&$value, $key) {
            $value = $key . ': ' . implode(', ', $value);
        });
        $headers = implode(PHP_EOL, $headers);
        return $headers;
    }
}