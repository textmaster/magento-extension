<?php
/**
 * TextMaster Configuration helper
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Helper;

use TextMaster\TextMaster\Api\Data\TranslatableContentInterface as TranslatableContent;
use TextMaster\TextMaster\Api\TranslatableContentRepositoryInterface as TranslatableContentRepository;
use TextMaster\TextMaster\Model\Project;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use TextMaster\TextMaster\Api\LanguageMappingRepositoryInterface as LanguageMappingRepository;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\UrlInterface;

class Configuration
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Connector
     */
    protected $connectorHelper;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @var array
     */
    protected $templates;

    /**
     * @var LanguageMappingRepository
     */
    protected $languageMappingRepository;

    /**
     * @var TranslatableContentRepository
     */
    protected $translatableContentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Configuration constructor.
     * @param Data $dataHelper
     * @param Connector $connectorHelper
     * @param MessageManager $messageManager
     * @param LanguageMappingRepository $languageMappingRepository
     * @param TranslatableContentRepository $translatableContentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreRepositoryInterface $storeRepository
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Data $dataHelper,
        Connector $connectorHelper,
        MessageManager $messageManager,
        LanguageMappingRepository $languageMappingRepository,
        TranslatableContentRepository $translatableContentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreRepositoryInterface $storeRepository,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->dataHelper = $dataHelper;
        $this->connectorHelper = $connectorHelper;
        $this->messageManager = $messageManager;
        $this->languageMappingRepository = $languageMappingRepository;
        $this->translatableContentRepository = $translatableContentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeRepository = $storeRepository;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function checkConfiguration(): bool
    {
        $checkConfiguration = true;

        if (!$this->hasApiKeyAndApiSecret()) {
            $this->messageManager->addComplexWarningMessage(
                'hasNoApiKeyOrApiSecretMessage',
                [
                    'url' => $this->urlBuilder->getUrl($this->dataHelper->getAuthentificationConfigurationUrl())
                ]
            );
            return false;
        }

        if (!$this->hasTemplates()) {
            $this->messageManager->addComplexWarningMessage(
                'hasNoApiTemplateMessage',
                [
                    'url' => $this->dataHelper->getApiTemplatesPageUrl()
                ]
            );
            $checkConfiguration = false;
        }

        if (!$this->hasTranslatableContent()) {
            $this->messageManager->addComplexWarningMessage(
                'hasNoTranslatableContentMessage',
                [
                    'url' => $this->urlBuilder->getUrl($this->dataHelper->getTranslatableContentUrl())
                ]
            );
            $checkConfiguration = false;
        }

        if ($notAvailableLanguageMappings = $this->getNotAvailableLanguageMappings()) {
            $this->messageManager->addComplexWarningMessage(
                'hasNotAvailableLanguageMappingMessage',
                [
                    'url' => $this->urlBuilder->getUrl($this->dataHelper->getLanguageMappingUrl()),
                    'not_available_language_mappings' => implode(', ', $notAvailableLanguageMappings)
                ]
            );
            $checkConfiguration = false;
        }

        if (!$this->hasMultiStores()) {
            $this->messageManager->addComplexWarningMessage(
                'hasNoMultiStoresMessage',
                [
                    'url' => $this->urlBuilder->getUrl($this->dataHelper->getStoreConfigurationUrl())
                ]
            );
            $checkConfiguration = false;
        }

        return $checkConfiguration;
    }

    /**
     * @return array
     */
    protected function getTemplates()
    {
        if ($this->templates === null) {
            try {
                foreach ($this->connectorHelper->getTemplates() as $template) {
                    $this->templates[$template['id']] = [
                        Project::FIELD_SOURCE_LANGUAGE => $template['languages'][0]['sourceLanguage'],
                        Project::FIELD_TARGET_LANGUAGE => $template['languages'][0]['targetLanguages'][0],
                        Project::FIELD_LANGUAGE_LEVEL => $template['options']['languageLevel']
                    ];
                }
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
            if ($this->templates === null) {
                $this->templates = [];
            }
        }
        return $this->templates;
    }

    /**
     * @param string $templateId
     * @param string $dataKey
     *
     * @return string
     */
    public function getTemplateData(string $templateId, string $dataKey): string
    {
        $templates = $this->getTemplates();
        return $templates[$templateId][$dataKey] ?? '';
    }

    /**
     * @return bool
     */
    public function hasTemplates(): bool
    {
        return count($this->getTemplates()) > 0;
    }

    /**
     * @return bool
     */
    public function hasApiKeyAndApiSecret(): bool
    {
        if (!empty($this->dataHelper->getApiKey()) && !empty($this->dataHelper->getApiSecret())) {
            return true;
        }
        return false;
    }

    /**
     * @return array | bool
     */
    public function getNotAvailableLanguageMappings()
    {
        $availableLanguageMapping = [];
        $notAvailableLanguageMapping = [];
        $languageMappingList = $this->languageMappingRepository->getList();
        foreach ($languageMappingList->getItems() as $languageMapping) {
            $availableLanguageMapping[] = $languageMapping->getMagentoLocaleLanguage();
        }

        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() != Store::DEFAULT_STORE_ID) {
                $lang = $this->scopeConfig->getValue(
                    'general/locale/code',
                    ScopeInterface::SCOPE_STORE,
                    $store
                );
                if (!in_array($lang, $availableLanguageMapping)) {
                    $notAvailableLanguageMapping[] = $lang;
                }
            }
        }
        return count($notAvailableLanguageMapping) ? $notAvailableLanguageMapping : false;
    }

    /**
     * @return bool
     */
    public function hasTranslatableContent(): bool
    {
        foreach (TranslatableContent::DOCUMENT_TYPE_LIST as $documentType) {
            $this->searchCriteriaBuilder->addFilter(
                TranslatableContent::FIELD_DOCUMENT_TYPE,
                $documentType,
                'eq'
            );

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $totalCount = $this->translatableContentRepository->getList($searchCriteria)->getTotalCount();
            $hasAttributeTranslatable = $totalCount >= 1;
            if ($hasAttributeTranslatable === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function hasMultiStores(): bool
    {
        $storeIds = [];

        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() != Store::DEFAULT_STORE_ID) {
                $storeIds[] = $store->getId();
            }
        }
        return count($storeIds) > 1;
    }

    /**
     * @return Connector
     */
    public function getConnectorHelper(): Connector
    {
        return $this->connectorHelper;
    }

    /**
     * @return Data
     */
    public function getDataHelper(): Data
    {
        return $this->dataHelper;
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }
}
