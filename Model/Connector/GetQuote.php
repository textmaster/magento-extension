<?php
/**
 * Class Get Quote Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetQuote extends AbstractService
{
    const API_ENDPOINT = 'projects/%s/quote';

    /**
     * @var string
     */
    protected $projectId;

    /**
     * @return string|null
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     *
     * @return GetQuote
     */
    public function setProjectId(string $projectId): GetQuote
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * projects/{projectId}/quote return quote information for the specified project
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = sprintf($this->getUrl(), $this->getProjectId());
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
