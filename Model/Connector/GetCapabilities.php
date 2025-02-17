<?php
/**
 * Class GetCapabilities Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetCapabilities extends AbstractService
{
    const API_ENDPOINT = 'lookups/capabilities';

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = $this->getUrl();
        $this->curlClient->setHeaders($this->getHeaders(true));
        $this->curlClient->get($uri);
        $this->checkResponse();
    }

    /**
     * @return mixed
     */
    public function getEndpoint(): string
    {
        return self::API_ENDPOINT;
    }
}
