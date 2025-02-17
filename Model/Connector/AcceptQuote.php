<?php
/**
 * Class Accept Quote Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Exception;

class AcceptQuote extends AbstractService
{
    const API_ENDPOINT = 'projects/%s/quote/status';

    const QUOTE_STATUS_ACCEPTED = 'accepted';

    const QUOTE_STATUS_REJECTED = 'rejected';

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
     * @return AcceptQuote
     */
    public function setProjectId(string $projectId): AcceptQuote
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @param $status
     */
    public function setQuoteStatus($status)
    {
        $quote = [];
        $quote['status'] = $status;
        $quote['comment'] = '';

        $this->setParams(
            $this->serializer->serialize($quote)
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $uri = sprintf($this->getUrl(), $this->getProjectId());
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
