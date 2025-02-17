<?php
/**
 * Class Get Information Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetInformation extends AbstractService
{
    const API_ENDPOINT = 'clients/users/me';

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $this->setUrl($this->dataHelper->getApiTmsUrl($this->getEndpoint()));
        $this->curlClient->setHeaders($this->getHeaders(true, true));
        $this->curlClient->get($this->getUrl());
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
