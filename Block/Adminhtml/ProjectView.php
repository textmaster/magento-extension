<?php
/**
 * Project View Block
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Block\Adminhtml;

use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TextMaster\TextMaster\Helper\Data as TextMasterHelper;
use Magento\Framework\Exception\NoSuchEntityException;

class ProjectView extends Template
{
    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepository;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var TextMasterHelper
     */
    protected $textmasterHelper;

    /**
     * @var ConnectorHelper
     */
    protected $connectorHelper;

    /**
     * @var ProjectInterface
     */
    protected $project;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * ProjectView constructor.
     * @param Template\Context $context
     * @param ProjectRepositoryInterface $projectRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param TimezoneInterface $timezoneInterface
     * @param TextMasterHelper $textmasterHelper
     * @param ConnectorHelper $connectorHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProjectHelper $projectHelper
     * @param MessageManager $messageManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ProjectRepositoryInterface $projectRepository,
        StoreRepositoryInterface $storeRepository,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        TimezoneInterface $timezoneInterface,
        TextMasterHelper $textmasterHelper,
        ConnectorHelper $connectorHelper,
        PriceCurrencyInterface $priceCurrency,
        ProjectHelper $projectHelper,
        MessageManager $messageManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->projectRepository = $projectRepository;
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->timezoneInterface = $timezoneInterface;
        $this->textmasterHelper = $textmasterHelper;
        $this->connectorHelper = $connectorHelper;
        $this->priceCurrency = $priceCurrency;
        $this->projectHelper = $projectHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return (int)$this->getRequest()->getParam(ProjectInterface::FIELD_PROJECT_ID);
    }

    /**
     * @return ProjectInterface
     * @throws NoSuchEntityException
     */
    public function getProjectById(): ProjectInterface
    {
        if ($this->project === null) {
            $projectId = $this->getProjectId();
            $this->project = $this->projectRepository->getById($projectId);
        }
        return $this->project;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTextMasterTemplateName(): string
    {
        $templateId = $this->getProjectById()->getTemplateId();
        try {
            foreach ($this->connectorHelper->getTemplates() as $template) {
                if ($template['id'] === $templateId) {
                    return (string)__($template['name']);
                }
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        return '';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPrice()
    {
        if ($this->getProjectById()->getPrice()) {
            return $this->priceCurrency->format(
                $this->getProjectById()->getPrice(),
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $this->getProjectById()->getCurrency()
            );
        }
        return '';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSourceLanguage(): string
    {
        $sourceStoreId = (int)$this->getProjectById()->getSourceStoreId();
        return $this->getWebsiteLanguage($sourceStoreId);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTargetLanguage(): string
    {
        $targetStoreId = (int)$this->getProjectById()->getTargetStoreId();
        return $this->getWebsiteLanguage($targetStoreId);
    }

    /**
     * @param $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getWebsiteLanguage($storeId): string
    {
        $store = $this->storeRepository->getById($storeId);
        $websiteLanguage = "";

        if ($store->getId() != Store::DEFAULT_STORE_ID) {
            $lang = $this->scopeConfig->getValue(
                'general/locale/code',
                ScopeInterface::SCOPE_STORE,
                $store
            );
            $websiteLanguage = $store->getWebsite()->getName() . ' - ' . $store->getName() . ' - ' . $lang;
        }
        return $websiteLanguage;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDueDateFormatted(): string
    {
        $dueDate = $this->getProjectById()->getDueDate();
        return $this->formatProjectDate($dueDate);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCreatedAtFormatted(): string
    {
        $createdAt = $this->getProjectById()->getCreatedAt();
        return $this->formatProjectDate($createdAt);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUpdatedAtFormatted(): string
    {
        $updatedAt = $this->getProjectById()->getCreatedAt();
        return $this->formatProjectDate($updatedAt);
    }

    /**
     * @param $dateTime
     * @return string
     */
    public function formatProjectDate($dateTime): string
    {
        return $this->formatDate($this->timezoneInterface->date($dateTime), \IntlDateFormatter::MEDIUM, true);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCategory(): string
    {
        $categoryCode = $this->getProjectById()->getCategory();
        $categories = $this->connectorHelper->getCategories();
        $categoryPairIndex = array_search($categoryCode, array_column($categories, 'code'));
        return $categories[$categoryPairIndex]['name'] ?? $categoryCode;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProjectPageUrl(): string
    {
        $project = $this->getProjectById();
        $storeId = $this->storeRepository->getById($project->getSourceStoreId());
        return sprintf($this->textmasterHelper->getProjectPageUrl($storeId), $project->getTextMasterId());
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function showValidateQuoteButton(): bool
    {
        $project = $this->getProjectById();
        if (($project->getStatus() === ProjectInterface::STATUS_IN_CREATION && !empty($project->getPrice())) &&
            (bool)$project->getAutolaunch() === false &&
            (bool)$project->getQuoteValidated() === false
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function showApplyTranslationButton(): bool
    {
        $project = $this->getProjectById();
        if (count($this->projectHelper->getTranslatableDocuments($project)->getItems()) ===
            count($this->projectHelper->getAllDocuments($project)->getItems()) &&
            !$this->projectHelper->isBeingTranslatedProject($project)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getProjectIdName(): string
    {
        return ProjectInterface::FIELD_PROJECT_ID;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getValidateQuoteAction(): string
    {
        $project = $this->getProjectById();
        return $this->getUrl(
            'textmaster/project/validateQuote',
            [ProjectInterface::FIELD_PROJECT_ID => $project->getProjectId()]
        );
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getApplyTranslationAction(): string
    {
        $project = $this->getProjectById();
        return $this->getUrl(
            'textmaster/project/applyTranslation',
            [ProjectInterface::FIELD_PROJECT_ID => $project->getProjectId()]
        );
    }
}
