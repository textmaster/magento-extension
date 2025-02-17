<?php
/**
 * Class Get Project Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetProject extends AbstractService
{
    const API_ENDPOINT = 'projects/';

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
     * @return GetProject
     */
    public function setProjectId(string $projectId): GetProject
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * projects/ return a list of arrays with all projects datas in each array
     * projects/{projectId} return an array with the datas of the specified project id
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = $this->getUrl() . $this->getProjectId();
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
