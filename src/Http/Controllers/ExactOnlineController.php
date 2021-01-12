<?php

namespace PolarisDC\Exact\ExactOnlineConnector\Http\Controllers;

use Illuminate\Http\Request;
use PolarisDC\Exact\ExactOnlineConnector\ExactOnlineService;

class ExactOnlineController extends Controller
{

    public function authorizeExactConnection(ExactOnlineService $service)
    {
        $service->authorizeClient();

        return 'De connectie met Exact is reeds geauthenticeerd!';
    }

    /**
     * @param Request            $request
     * @param ExactOnlineService $service
     * @return string
     */
    public function callbackAuthorizeExactConnection(Request $request, ExactOnlineService $service)
    {
        $success = $service->finishAuthorizationClient($request->get('code'));

        return $success ? 'De connectie met Exact is succesvol geauthenticeerd!' : 'De connectie met Exact is mislukt :( .';
    }

    public function disconnectExactConnection(ExactOnlineService $service)
    {
        $service->disconnectClient();
        return 'De connectie met Exact is succesvol gedisconnect!';
    }
}