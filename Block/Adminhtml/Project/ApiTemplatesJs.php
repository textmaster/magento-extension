<?php
/**
 * Api Templates Js Block
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Block\Adminhtml\Project;

use TextMaster\TextMaster\Api\LanguageMappingRepositoryInterface;
use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;
use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class ApiTemplatesJs extends Template
{
    /**
     * @var ConnectorHelper
     */
    protected $connectorHelper;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LanguageMappingRepositoryInterface
     */
    protected $languageMappingRepository;

    /**
     * @var array
     */
    protected $languageMapping;

    /**
     * @var array
     */
    protected $apiTemplates;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param ConnectorHelper $connectorHelper
     * @param SerializerInterface $serializer
     * @param StoreRepositoryInterface $storeRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param LanguageMappingRepositoryInterface $languageMappingRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConnectorHelper $connectorHelper,
        SerializerInterface $serializer,
        StoreRepositoryInterface $storeRepository,
        ScopeConfigInterface $scopeConfig,
        LanguageMappingRepositoryInterface $languageMappingRepository,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->connectorHelper = $connectorHelper;
        $this->serializer = $serializer;
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
        $this->languageMappingRepository = $languageMappingRepository;
        $this->loadLanguageMapping();
        $this->loadApiTemplates();
    }

    /**
     * @return string
     */
    public function getApiTemplatesConfigJson(): string
    {
        $config = [];
        foreach ($this->getStoresWithApiLocale() as $sourceStoreId => $sourceStoreApiLocale) {
            foreach ($this->getStoresWithApiLocale() as $targetStoreId => $targetStoreApiLocale) {
                if ($sourceStoreId !== $targetStoreId &&
                    $sourceStoreApiLocale !== $targetStoreApiLocale &&
                    $template = $this->getApiTemplate($sourceStoreApiLocale, $targetStoreApiLocale)
                ) {
                    $config[$sourceStoreId][$targetStoreId] = $template;
                }
            }
        }
        return $this->serializer->serialize($config);
    }

    /**
     * @return array
     */
    protected function getStoresWithApiLocale(): array
    {
        $stores = [];
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() != Store::DEFAULT_STORE_ID) {
                $lang = $this->scopeConfig->getValue(
                    'general/locale/code',
                    ScopeInterface::SCOPE_STORE,
                    $store
                );
                $stores[$store->getId()] = $this->getApiLocale($lang);
            }
        }
        return $stores;
    }

    /**
     * @return void
     */
    protected function loadLanguageMapping()
    {
        if ($this->languageMapping === null) {
            $languageMappingList = $this->languageMappingRepository->getList();
            foreach ($languageMappingList->getItems() as $languageMapping) {
                $this->languageMapping[
                    $languageMapping->getMagentoLocaleLanguage()
                ] = $languageMapping->getApiLocaleLanguage();
            }
        }
    }

    /**
     * @param string $magentoLocale
     *
     * @return string
     */
    protected function getApiLocale(string $magentoLocale): string
    {
        return $this->languageMapping[$magentoLocale] ?? '';
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function loadApiTemplates()
    {
        if ($this->apiTemplates === null) {
            foreach ($this->connectorHelper->getTemplates() as $template) {
                $sourceLanguage = $template['languages'][0]['sourceLanguage'];
                $targetLanguage = $template['languages'][0]['targetLanguages'][0];
                if (!isset($this->apiTemplates[$sourceLanguage][$targetLanguage])) {
                    $this->apiTemplates[$sourceLanguage][$targetLanguage] = [];
                }

                $this->apiTemplates[$sourceLanguage][$targetLanguage][] = [
                    'value' => $template['id'],
                    'label' => (string) __($template['name'])
                ];
            }
        }
    }

    /**
     * @param string $sourceApiLocale
     * @param string $targetApiLocale
     *
     * @return array|false
     */
    protected function getApiTemplate(string $sourceApiLocale, string $targetApiLocale)
    {
        return $this->apiTemplates[$sourceApiLocale][$targetApiLocale] ?? false;
    }
}
