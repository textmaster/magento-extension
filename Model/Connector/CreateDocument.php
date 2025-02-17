<?php
/**
 * Class Create Document Service
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
namespace TextMaster\TextMaster\Model\Connector;

use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use TextMaster\TextMaster\Helper\Data as DataHelper;
use Magento\Framework\App\Area;
use Exception;
use TextMaster\TextMaster\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\App\Emulation as StoreEmulation;
use Magento\Store\Model\StoreManagerInterface;

class CreateDocument extends AbstractService
{
    const API_ENDPOINT = 'clients/projects/%s/batch/documents';

    const TEXTMASTER_DOCUMENT_CALLBACKS = [
        'waiting_assignment',
        'in_progress',
        'in_review',
        'incomplete',
        'completed',
        'paused',
        'canceled',
        'quality_control',
        'copyscape',
        'counting_words',
        'word_count_finished',
        'support_message_created'
    ];

    const MAGENTO_DOCUMENT_CALLBACK_URL = 'rest/V1/textmaster-textmaster/document/token/%s/callback/%s';

    /**
     * @var string
     */
    protected $projectId;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var StoreEmulation
     */
    protected $storeEmulation;

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     *
     * @return CreateDocument
     */
    public function setProjectId(string $projectId): CreateDocument
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * CreateDocument constructor.
     * @param Curl $curlClient
     * @param SerializerInterface $serializer
     * @param DataHelper $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param PageRepositoryInterface $pageRepository
     * @param BlockRepositoryInterface $blockRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreEmulation $storeEmulation
     */
    public function __construct(
        Curl $curlClient,
        SerializerInterface $serializer,
        DataHelper $dataHelper,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        PageRepositoryInterface $pageRepository,
        BlockRepositoryInterface $blockRepository,
        CategoryRepositoryInterface $categoryRepository,
        StoreEmulation $storeEmulation
    ) {
        parent::__construct($curlClient, $serializer, $dataHelper, $storeManager);
        $this->productRepository = $productRepository;
        $this->pageRepository = $pageRepository;
        $this->blockRepository = $blockRepository;
        $this->categoryRepository = $categoryRepository;
        $this->storeEmulation = $storeEmulation;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $this->setUrl($this->dataHelper->getApiTmsUrl(sprintf($this->getEndpoint(), $this->getProjectId())));
        $uri = $this->getUrl();
        $this->curlClient->setHeaders($this->getHeaders(true, true));
        $this->curlClient->post($uri, $this->getParams());
        $this->checkResponse();
    }

    /**
     * Set attribute codes to send for document
     * Return true if at least one attribute code is translatable and set in the body's data to send
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @param $projectAttributeCodes
     * @return bool
     */
    public function setDocumentData(ProjectInterface $project, DocumentInterface $document, $projectAttributeCodes)
    {
        $attributeCodeExistingList = $this->getAttributeCodeExistingList($project, $document, $projectAttributeCodes);
        $documents = [];
        $documentData = [];
        $originalContent = [];
        $documentData['type'] = 'key_value';
        $documentData['title'] = $document->getName();
        $documentData['perform_word_count'] = true;

        foreach ($attributeCodeExistingList as $attributeCode => $value) {
            $originalContent[$attributeCode]['original_phrase'] = $value;
            $originalContent[$attributeCode]['details'] = $attributeCode;
            $documentData['original_content'][$attributeCode] = $originalContent[$attributeCode];
        }
        $documentData['callback'] = $this->getCallbacks(
            $document->getToken(),
            self::TEXTMASTER_DOCUMENT_CALLBACKS,
            self::MAGENTO_DOCUMENT_CALLBACK_URL,
            true
        );

        $documents['documents'][] = $documentData;

        $this->setParams(
            $this->serializer->serialize($documents)
        );

        return !empty($documents['documents'][0]['original_content']);
    }

    /**
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @param array $projectAttributeCodes
     *
     * @return array
     */
    public function getAttributeCodeExistingList(
        ProjectInterface $project,
        DocumentInterface $document,
        array $projectAttributeCodes
    ) {
        $attributeCodeExistingList = [];

        $this->storeEmulation->startEnvironmentEmulation($project->getSourceStoreId(), Area::AREA_FRONTEND, true);

        foreach ($projectAttributeCodes as $attributeCode) {
            $attributeCodeExisting = $this->getAttributeCodeValue($project, $document, $attributeCode);
            if (!empty($attributeCodeExisting)) {
                $attributeCodeExistingList[$attributeCode] = $attributeCodeExisting[$attributeCode];
            }
        }

        $this->storeEmulation->stopEnvironmentEmulation();

        return $attributeCodeExistingList;
    }

    /**
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @param string $attributeCode
     * @return array
     */
    public function getAttributeCodeValue(ProjectInterface $project, DocumentInterface $document, string $attributeCode)
    {
        $attributeCodeExisting = [];

        if ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_PRODUCT) {
            $attributeCodeExisting = $this->getProductAttributeCodeValue($project, $document, $attributeCode);
        } elseif ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_PAGE) {
            $attributeCodeExisting = $this->getPageAttributeCodeValue($document, $attributeCode);
        } elseif ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_BLOCK) {
            $attributeCodeExisting = $this->getBlockAttributeCodeValue($document, $attributeCode);
        } elseif ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_CATEGORY) {
            $attributeCodeExisting = $this->getCategoryAttributeCodeValue($project, $document, $attributeCode);
        }

        return $attributeCodeExisting;
    }

    /**
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @param string $attributeCode
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getProductAttributeCodeValue(
        ProjectInterface $project,
        DocumentInterface $document,
        string $attributeCode
    ) {
        $attributeCodeExisting = [];

        $productRepository = $this->productRepository->getById(
            $document->getMagentoEntityId(),
            false,
            $project->getSourceStoreId()
        );
        $attributeCodeValue = $productRepository->getData($attributeCode);

        if (!empty($attributeCodeValue)) {
            $attributeCodeExisting[$attributeCode] = $attributeCodeValue;
        }

        return $attributeCodeExisting;
    }

    /**
     * @param DocumentInterface $document
     * @param string $attributeCode

     * @return array
     * @throws LocalizedException
     */
    public function getPageAttributeCodeValue(DocumentInterface $document, string $attributeCode)
    {
        $attributeCodeExisting = [];

        $pageRepository = $this->pageRepository->getById($document->getMagentoEntityId());
        $attributeCodeValue = $pageRepository->getData($attributeCode);

        if (!empty($attributeCodeValue)) {
            $attributeCodeExisting[$attributeCode] = $pageRepository->getData($attributeCode);
        }

        return $attributeCodeExisting;
    }

    /**
     * @param DocumentInterface $document
     * @param string $attributeCode
     *
     * @return array
     * @throws LocalizedException
     */
    public function getBlockAttributeCodeValue(DocumentInterface $document, string $attributeCode)
    {
        $attributeCodeExisting = [];

        $blockRepository = $this->blockRepository->getById($document->getMagentoEntityId());
        $attributeCodeValue = $blockRepository->getData($attributeCode);

        if (!empty($attributeCodeValue)) {
            $attributeCodeExisting[$attributeCode] = $blockRepository->getData($attributeCode);
        }

        return $attributeCodeExisting;
    }

    /**
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @param string $attributeCode
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCategoryAttributeCodeValue(
        ProjectInterface $project,
        DocumentInterface $document,
        string $attributeCode
    ) {
        $attributeCodeExisting = [];

        $categoryRepository = $this->categoryRepository->get(
            $document->getMagentoEntityId(),
            $project->getSourceStoreId()
        );
        $attributeCodeValue = $categoryRepository->getData($attributeCode);

        if (!empty($attributeCodeValue)) {
            $attributeCodeExisting[$attributeCode] = $categoryRepository->getData($attributeCode);
        }

        return $attributeCodeExisting;
    }

    /**
     * @return mixed
     */
    public function getEndpoint(): string
    {
        return self::API_ENDPOINT;
    }
}
