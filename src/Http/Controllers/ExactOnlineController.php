<?php

namespace PolarisDC\ExactOnline\LaravelClient\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Picqer\Financials\Exact\Item;
use PolarisDC\ExactOnline\BaseClient\ExactOnlineClient;
use PolarisDC\ExactOnline\BaseClient\Exceptions\AuthenticationException;
use PolarisDC\ExactOnline\BaseClient\Exceptions\ExactOnlineClientException;

class ExactOnlineController
{
    /**
     * @throws ExactOnlineClientException
     */
    public function startAuthorization(ExactOnlineClient $client): JsonResponse
    {
        $client->startAuthorization();

        return response()->json(['status' => 'Connection was already authenticated.']);
    }

    public function completeAuthorization(Request $request, ExactOnlineClient $client)
    {
        try {
            if (! $request->get('code')) {
                return response()->json(['status' => 'No "code" received during callback from EOL. Authentication failed.']);
            }

            $client->completeAuthorization($request->get('code'));

            return redirect()->route('exact-online.test');

        } catch (ExactOnlineClientException $e) {
            abort(500, $e->getMessage());
        }

        abort(500, 'Unknown authentication error occurred.');
    }

    /**
     * @throws ExactOnlineClientException
     * @throws AuthenticationException
     */
    public function test(ExactOnlineClient $client): JsonResponse
    {
        new Item($client->getConnection());

        return response()->json('API connection successful.');
    }

    public function disconnect(ExactOnlineClient $client): JsonResponse
    {
        $client->disconnect();

        return response()->json(['status' => 'Connection was revoked.']);
    }
}