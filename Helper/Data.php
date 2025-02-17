<?php
/**
 * TextMaster module base helper
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Data extends AbstractHelper
{
    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_AUTHENTICATION_ENVIRONMENT path of textmaster environment
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_AUTHENTICATION_ENVIRONMENT =
        'textmaster_textmaster/authentication/environment';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_PAYMENT_REQUESTS path of payment requests url
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_PAYMENT_REQUESTS = 'textmaster_textmaster/%s/url/payment_requests';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_DOCUMENT_PAGE path of document page url
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_DOCUMENT_PAGE = 'textmaster_textmaster/%s/url/document_page';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_PROJECT_PAGE path of project page url
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_PROJECT_PAGE = 'textmaster_textmaster/%s/url/project_page';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_API_TEMPLATES_PAGE path of api templates page url
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_API_TEMPLATES_PAGE = 'textmaster_textmaster/%s/url/api_templates_page';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_URL path of textmaster api url
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_URL = 'textmaster_textmaster/api/url';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_TMS path of textmaster api tms
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_TMS = 'textmaster_textmaster/api/tms';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_TMS_URL path of textmaster api tms url
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_URL_TMS = 'textmaster_textmaster/api/url_tms';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_HOST path of textmaster api host
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_HOST = 'textmaster_textmaster/%s/api/host';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_HOST_TMS path of textmaster api host
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_HOST_TMS = 'textmaster_textmaster/%s/api/host_tms';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_KEY path of textmaster api key
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_KEY = 'textmaster_textmaster/authentication/%s_api_key';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_SECRET path of textmaster api secret
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_API_SECRET = 'textmaster_textmaster/authentication/%s_api_secret';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_TRANSLATABLE_CONTENT_URL path of translatable content listing
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_TRANSLATABLE_CONTENT_URL = 'textmaster/translatablecontent/index';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_AUTHENTIFICATION_CONFIGURATION_URL
     * path of authentification configuration page
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_AUTHENTIFICATION_CONFIGURATION_URL =
        'adminhtml/system_config/edit/section/textmaster_textmaster';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_LANGUAGE_MAPPING_URL path of language mapping listing
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_LANGUAGE_MAPPING_URL = 'textmaster/languagemapping/index';

    /**
     * const XML_CONFIG_TEXTMASTER_TEXTMASTER_STORE_CONFIGURATION_URL path of store configuration
     */
    const XML_CONFIG_TEXTMASTER_TEXTMASTER_STORE_CONFIGURATION_URL = 'adminhtml/system_store/index';

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * Data constructor.
     * @param Context $context
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezoneInterface
    ) {
        parent::__construct($context);
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * add environment to config path
     *
     * @param $path
     * @param null $storeId
     *
     * @return string
     */
    protected function applyEnvironment($path, $storeId = null): string
    {
        $environment = $this->getEnvironment($storeId);
        return sprintf($path, $environment);
    }

    /**
     * Get Payment Requests Page Url
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getPaymentRequestsUrl($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_PAYMENT_REQUESTS, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Document Page Url
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getDocumentPageUrl($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_DOCUMENT_PAGE, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Project Page Url
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getProjectPageUrl($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_PROJECT_PAGE, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Api Templates Page Url
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getApiTemplatesPageUrl($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_URL_API_TEMPLATES_PAGE, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * TextMaster Environment
     *
     * @param int|null $storeId

     * @return string
     */
    public function getEnvironment($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_TEXTMASTER_TEXTMASTER_AUTHENTICATION_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Api TMS
     *
     * @param int|null $storeId

     * @return string
     */
    public function getApiTms($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_TMS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Translatable Content Url
     *
     * @return string
     */
    public function getTranslatableContentUrl(): string
    {
        return static::XML_CONFIG_TEXTMASTER_TEXTMASTER_TRANSLATABLE_CONTENT_URL;
    }

    /**
     * Get Authentification Configuration Url
     *
     * @return string
     */
    public function getAuthentificationConfigurationUrl(): string
    {
        return static::XML_CONFIG_TEXTMASTER_TEXTMASTER_AUTHENTIFICATION_CONFIGURATION_URL;
    }

    /**
     * Get Language Mapping Url
     *
     * @return string
     */
    public function getLanguageMappingUrl(): string
    {
        return static::XML_CONFIG_TEXTMASTER_TEXTMASTER_LANGUAGE_MAPPING_URL;
    }

    /**
     * Get Store Configuration Url
     *
     * @return string
     */
    public function getStoreConfigurationUrl(): string
    {
        return static::XML_CONFIG_TEXTMASTER_TEXTMASTER_STORE_CONFIGURATION_URL;
    }

    /**
     * Get Api Url
     *
     * @param string   $endpoint
     * @param int|null $storeId
     *
     * @return string
     */
    public function getApiUrl(string $endpoint, $storeId = null): string
    {
        $apiUrl = $this->scopeConfig->getValue(
            self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $apiHost = $this->getApiHost($storeId);
        $apiTms = $this->getApiTms($storeId);
        $apiUrl = sprintf($apiUrl, $apiHost, $apiTms);
        return $apiUrl . $endpoint;
    }

    /**
     * Get Api TMS Url
     *
     * @param int|null $storeId

     * @return string
     */
    public function getApiTmsUrl(string $endpoint, $storeId = null): string
    {
        $apiUrlTms = $this->scopeConfig->getValue(
            self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_URL_TMS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $apiHostTms = $this->getApiHostTms($storeId);
        $apiUrl = sprintf($apiUrlTms, $apiHostTms);
        return $apiUrl . $endpoint;
    }

    /**
     * Get Api Host Url
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getApiHost($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_HOST, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Api Host TMS Url
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getApiHostTms($storeId = null): string
    {
        return $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_HOST_TMS, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Api Key
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getApiKey($storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_KEY, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Api Secret
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getApiSecret($storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->applyEnvironment(self::XML_CONFIG_TEXTMASTER_TEXTMASTER_API_SECRET, $storeId),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $datetime
     * @return string
     */
    public function formatDateForApiCall($datetime): string
    {
        return $this->timezoneInterface->date($datetime)->format('Y-m-d');
    }
}
