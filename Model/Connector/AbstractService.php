<?php
/**
 * Class Abstract Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use Magento\Framework\Serialize\SerializerInterface;
use TextMaster\TextMaster\HTTP\Client\Curl;
use TextMaster\TextMaster\Helper\Data as DataHelper;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractService
{
    /**
     * Response status error message
     */
    const API_RESPONSE_STATUS_ERROR = 'ERROR';

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array|string
     */
    protected $params = '';

    /**
     * @param Curl                  $curlClient
     * @param SerializerInterface   $serializer
     * @param DataHelper            $dataHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Curl                $curlClient,
        SerializerInterface $serializer,
        DataHelper          $dataHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->curlClient   = $curlClient;
        $this->serializer   = $serializer;
        $this->dataHelper   = $dataHelper;
        $this->storeManager = $storeManager;
        $this->setUrl($this->dataHelper->getApiUrl($this->getEndpoint()));
    }

    /**
     * @param bool $setContentType
     * @param bool $tms
     *
     * @return array
     */
    public function getHeaders($setContentType = false, $tms = false): array
    {
        $headers = [];

        $headers['Accept'] = 'application/json';

        if ($setContentType) {
            $headers['Content-Type'] = 'application/json';
        }

        if (!$tms) {
            if ($clientId = $this->getClientId()) {
                $headers['client-id'] = $clientId;
            }

            if ($clientSecret = $this->getClientSecret()) {
                $headers['client-secret'] = $clientSecret;
            }
        }

        if ($tms) {
            if ($apiKey = $this->getClientId()) {
                $headers['ApiKey'] = $apiKey;
            }

            if ($date = $this->getHeadersDate()) {
                $headers['Date'] = $date;
            }

            if ($signature = $this->getApiSignature()) {
                $headers['Signature'] = $signature;
            }
        }

        $headers['platform_name'] = $this->getPlatformName();

        return $headers;
    }

    public function getPlatformName()
    {
        $platformName = 'b67c9591-8ab4-4e4b-b259-824e5a5a2ac1';
        return $platformName;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return AbstractService
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array|string $params
     * @return AbstractService
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param array $params
     * @return AbstractService
     */
    public function addParams($params)
    {
        if (!is_array($this->params)) {
            $this->params = [];
        }
        $this->params = array_merge_recursive($this->params, $params);
        return $this;
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->curlClient->getHeaders();
    }

    /**
     * @return array
     */
    public function getResponseBody()
    {
        if (!empty($this->curlClient->getBody())) {
            return $this->serializer->unserialize(
                $this->curlClient->getBody()
            );
        }
        return [];
    }

    /**
     * @return string
     */
    protected function getClientId()
    {
        return $this->dataHelper->getApiKey();
    }

    /**
     * @return string
     */
    protected function getClientSecret()
    {
        return $this->dataHelper->getApiSecret();
    }

    /**
     * @return string
     */
    public function getHeadersDate()
    {
        return gmdate("Y/m/d H:i:s", time()). ' UTC';
    }

    /**
     * @return string
     */
    public function getApiSignature()
    {
        return sha1($this->getClientSecret() . $this->getHeadersDate());
    }

    abstract public function getEndpoint(): string;

    abstract public function execute();

    /**
     * @return void
     * @throws LocalizedException
     */
    public function checkResponse()
    {
        $response = $this->getResponseBody();
        if (isset($response['response']['status']) &&
            $response['response']['status'] === static::API_RESPONSE_STATUS_ERROR
        ) {
            throw new LocalizedException(__('[API Textmaster][Error]: '.$this->getEndpoint()));
        }
    }

    /**
     * @return Phrase|string
     */
    public function getErrorMessages()
    {
        $errorMessage = '';
        $response = $this->getResponseBody();
        if (isset($response['response']['message'])) {
            $additionalDetails = '';
            if (isset($response['response']['additionalDetails'])) {
                $additionalDetails = $response['response']['additionalDetails'];
            }
            if (is_array($additionalDetails)) {
                $additionalDetails = $this->removeRedundantErrorLevels($additionalDetails);
                if (!is_array($additionalDetails)) {
                    $errorMessage .= ' ' . $additionalDetails;
                }
                foreach ($additionalDetails['errors'] as $errorType => $messages) {
                    $errorMessage .= ' [' . $errorType . '] :: ';
                    foreach ($messages as $errorMessages) {
                        $errorMessage .= $errorMessages . PHP_EOL;
                    }
                }
            } else {
                $errorMessage .= ' ' . $additionalDetails;
            }

        } else {
            $errorMessage = __('There was an error while processing the request');
        }
        return $errorMessage;
    }

    /**
     * @param $data array
     * @return mixed|string
     */
    private function removeRedundantErrorLevels($data)
    {
        if (array_key_exists('errors', $data)
            && is_array($data['errors'])
        ) {
            return $data;
        }
        if (array_key_exists('response', $data)
            && is_array($data['response'])
            && array_key_exists('additionalDetails', $data['response'])
        ) {
            $data = $this->removeRedundantErrorLevels($data['response']['additionalDetails']);
            return $data;
        }
        if (is_string($data)) {
            return $data;
        }
        return 'Unexpected error while parsing accolad error logs';
    }

    /**
     * @param string $token
     * @param array $textmasterCallbacks
     * @param string $url
     * @param boolean $withAttributeUrl
     *
     * @return array|null
     */
    public function getCallbacks(
        string $token,
        array $textmasterCallbacks,
        string $url,
        $withAttributeUrl = false
    ): ?array {
        $callbacks = [];
        $baseUrl = $this->storeManager->getDefaultStoreView()->getBaseUrl();
        foreach ($textmasterCallbacks as $callback) {
            $callbackUrl = $baseUrl . sprintf($url, $token, $callback);
            $callbacks[$callback]['url'] = $callbackUrl;
            if (!$withAttributeUrl) {
                $callbacks[$callback] = $callbackUrl;
            }
        }
        return count($callbacks) ? $callbacks : null;
    }
}
