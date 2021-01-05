<?php

namespace PolarisDC\Exact\ExactOnlineConnector\Http\Controllers;

use Illuminate\Http\Request;

class ExactOnlineController extends Controller
{

    public function authorizeExactConnection()
    {
        return 'De connectie met Exact is reeds geauthenticeerd!';
    }

    /**
     * @param Request $request
     * @return string
     */
    public function callbackAuthorizeExactConnection(Request $request)
    {
        $success =  true;

        return $success ? 'De connectie met Exact is succesvol geauthenticeerd!' : 'De connectie met Exact is mislukt :( .';
    }

    public function disconnectExactConnection()
    {
        return 'De connectie met Exact is succesvol gedisconnect!';
    }
}