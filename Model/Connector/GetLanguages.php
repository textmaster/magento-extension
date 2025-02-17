<?php
/**
 * Class Get Languages Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetLanguages extends AbstractService
{
    const API_ENDPOINT = 'lookups/languages';

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
