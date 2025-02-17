<?php
/**
 * Class GetCategories Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetCategories extends AbstractService
{
    const API_ENDPOINT = 'lookups/categories';

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
