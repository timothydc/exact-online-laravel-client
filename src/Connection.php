<?php
declare(strict_types=1);

namespace PolarisDC\ExactOnline\ExactOnlineClient;

use GuzzleHttp\Middleware;
use Picqer\Financials\Exact\Connection as PicqerConnection;
use Psr\Http\Message\RequestInterface;

class Connection extends PicqerConnection
{
    private $exactClientId;
    private $exactWebhookSecret;
    private $redirectUrl;


    /**
     * Sets the language sensitive properties such as descriptions in a specific language.
     *
     * @see https://start.exactonline.nl/docs/HlpRestAPIResourcesDetails.aspx?name=LogisticsItems#goodToKnow
     *
     * @param string $language
     */
    public function setCustomDescriptionLanguage(string $language)
    {
        $this->insertMiddleWare(Middleware::mapRequest(fn (RequestInterface $request) => $request->withHeader('CustomDescriptionLanguage', $language)));
    }

    public function getExactClientId()
    {
        return $this->exactClientId;
    }

    public function setExactClientId($exactClientId)
    {
        parent::setExactClientId($exactClientId);
        $this->exactClientId = $exactClientId;
    }

    public function getExactWebhookSecret()
    {
        return $this->exactWebhookSecret;
    }

    public function setExactWebhookSecret(string $webhookSecret)
    {
        $this->exactWebhookSecret = $webhookSecret;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl($redirectUrl)
    {
        parent::setRedirectUrl($redirectUrl);
        $this->redirectUrl = $redirectUrl;
    }
}