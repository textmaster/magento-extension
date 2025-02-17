<?php
/**
 * Class Analysis Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class Analysis extends AbstractService
{
    const API_ENDPOINT = 'projects/%s/analysis';

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
     * @return Analysis
     */
    public function setProjectId(string $projectId): Analysis
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function execute()
    {
        $uri = sprintf($this->getUrl(), $this->getProjectId());
        $this->curlClient->setHeaders($this->getHeaders(true));
        $this->curlClient->put($uri, $this->getParams());
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
