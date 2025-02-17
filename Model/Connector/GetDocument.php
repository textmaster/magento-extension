<?php
/**
 * Class Get Document Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class GetDocument extends AbstractService
{
    const API_ENDPOINT = 'projects/%s/files/targetFiles';

    /**
     * @var string
     */
    protected $projectId;

    /**
     * @var string
     */
    protected $documentId;

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
     * @return GetDocument
     */
    public function setProjectId(string $projectId): GetDocument
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param string $documentId
     *
     * @return GetDocument
     */
    public function setDocumentId(string $documentId): GetDocument
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * projects/{projectId}/files/targetFiles return a list of arrays of all documents for specified project id
     * projects/{projectId}/files/targetFiles/{documentId} return an array with the datas of the specified document id
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = sprintf($this->getUrl(), $this->getProjectId());
        if ($this->getDocumentId()) {
            $uri .= '/' . $this->getDocumentId();
        }
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
