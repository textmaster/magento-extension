<?php
/**
 * Class Accept Document Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class AcceptDocument extends AbstractService
{
    const API_ENDPOINT = 'projects/%s/files/targetFiles/%s/status';

    const STATUS_ACCEPTED = 'accepted';

    const STATUS_REJECTED = 'rejected';

    /**
     * @var string
     */
    protected $projectId;

    /**
     * @var string
     */
    protected $documentId;

    /**
     * @return string
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     *
     * @return AcceptDocument
     */
    public function setProjectId(string $projectId): AcceptDocument
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentId(): string
    {
        return $this->documentId;
    }

    /**
     * @param string $documentId
     *
     * @return AcceptDocument
     */
    public function setDocumentId(string $documentId): AcceptDocument
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * @param string $status
     */
    public function setDocumentStatus(string $status)
    {
        $document = [];
        $document['status'] = $status;

        $this->setParams(
            $this->serializer->serialize($document)
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = sprintf($this->getUrl(), $this->getProjectId(), $this->getDocumentId());
        $this->curlClient->setHeaders($this->getHeaders(true));
        $this->curlClient->post($uri, $this->getParams());
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
