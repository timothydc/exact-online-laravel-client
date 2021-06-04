<?php

namespace PolarisDC\ExactOnline\ExactOnlineClient\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PolarisDC\ExactOnline\ExactOnlineClient\ExactOnlineService;
use PolarisDC\ExactOnlineConnector\ExactOnlineConnector;
use PolarisDC\ExactOnlineConnector\Exceptions\ExactOnlineConnectorException;

class ExactOnlineController extends Controller
{
    public function startAuthorization(ExactOnlineConnector $exactOnlineConnector): JsonResponse
    {
        $exactOnlineConnector->startAuthorization();

        return response()->json(['status' => 'Connection was already authenticated.']);
    }

    public function completeAuthorization(Request $request, ExactOnlineConnector $exactOnlineConnector): JsonResponse
    {
        try {
            if ($exactOnlineConnector->completeAuthorization($request->get('code') . 'fout')) {
                return response()->json(['status' => 'Authentication completed.']);
            }
        } catch (ExactOnlineConnectorException $e) {
            abort(500, $e->getMessage());
        }

        abort(500, 'Unknown authentication error occurred.');
    }

    public function disconnect(ExactOnlineConnector $exactOnlineConnector): JsonResponse
    {
        $exactOnlineConnector->disconnect();

        return response()->json(['status' => 'Connection was revoked.']);
    }
}