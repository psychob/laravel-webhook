<?php

namespace PsychoB\WebHook\Http\Controllers;

use PsychoB\WebHook\Models\ReceivedRequest;
use PsychoB\WebHook\Services\UuidService;
use PsychoB\WebHook\Models\Request as WebRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @resource WebHook
 */
class WebHookController extends Controller
{
    /**
     * Endpoint for receiving webhooks from outside
     *
     * @param Request $webRequest
     * @return array
     */
    public function receive(Request $webRequest)
    {
        $request = new ReceivedRequest([
            'uuid' => UuidService::random(),
            'data' => $webRequest->getContent(false),
        ]);

        if ($webRequest->has('payload_uuid')) {
            $request->payload_uuid = $webRequest->get('payload_uuid');
        }

        if ($webRequest->has('request_uuid')) {
            $request->request_uuid = $webRequest->get('request_uuid');
        }

        $request->save();

        return [
            'message' => 'ok'
        ];
    }

    /**
     * Return all webhooks that were received by application
     *
     * @return mixed
     */
    public function listInbounds()
    {
        $paginator = ReceivedRequest::orderBy('updated_at', 'DESC')->paginate(15);
        $paginator->getCollection()->transform(function ($element) {
            $e = $element->toArray();

            $transformed = json_decode($e['data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $e['data'] = $transformed;
            }

            return $e;
        });

        return $paginator;
    }

    /**
     * Return all webhooks that were send by application
     *
     * @return mixed
     */
    public function listOutbounds()
    {
        $paginator = WebRequest::orderBy('updated_at', 'DESC')->paginate(15);
        $paginator->getCollection()->transform(function ($element) {
            $e = $element->toArray();

            $transformed = json_decode($e['request_body'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $e['request_body'] = $transformed;
            }

            $transformed = json_decode($e['response_body'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $e['response_body'] = $transformed;
            }

            return $e;
        });

        return $paginator;
    }
}
