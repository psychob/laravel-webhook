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
        return ReceivedRequest::orderBy('updated_at', 'DESC')->paginate(15);
    }

    /**
     * Return all webhooks that were send by application
     *
     * @return mixed
     */
    public function listOutbounds()
    {
        return WebRequest::orderBy('updated_at', 'DESC')->paginate(15);
    }
}