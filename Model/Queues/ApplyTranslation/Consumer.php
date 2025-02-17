<?php
/**
 * Apply Translation Consumer Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Queues\ApplyTranslation;

use TextMaster\TextMaster\Api\ConsumerInterface;
use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use TextMaster\TextMaster\Api\MessageInterface;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface as ProjectRepository;
use TextMaster\TextMaster\Api\DocumentRepositoryInterface as DocumentRepository;
use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;
use TextMaster\TextMaster\Model\Connector\AcceptDocument;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\GetBlockByIdentifierInterface as BlockByIdentifier;
use Magento\Cms\Api\GetPageByIdentifierInterface as PageByIdentifier;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation as StoreEmulation;
use Psr\Log\LoggerInterface;

class Consumer implements ConsumerInterface
{
    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var DocumentRepository */
    protected $documentRepository;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var CategoryRepositoryInterface */
    protected $categoryRepository;

    /** @var PageRepositoryInterface */
    protected $pageRepository;

    /** @var PageByIdentifier */
    protected $pageByIdentifier;

    /** @var BlockRepositoryInterface */
    protected $blockRepository;

    /** @var BlockByIdentifier */
    protected $blockByIdentifier;

    /** @var ConfigurationHelper */
    protected $configurationHelper;

    /** @var ProjectHelper */
    protected $projectHelper;

    /** @var StoreEmulation */
    protected $storeEmulation;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param ProjectRepository           $projectRepository
     * @param DocumentRepository          $documentRepository
     * @param ProductRepositoryInterface  $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PageRepositoryInterface     $pageRepository
     * @param PageByIdentifier            $pageByIdentifier
     * @param BlockRepositoryInterface    $blockRepository
     * @param BlockByIdentifier           $blockByIdentifier
     * @param ConfigurationHelper         $configurationHelper
     * @param ProjectHelper               $projectHelper
     * @param StoreEmulation              $storeEmulation
     * @param LoggerInterface             $logger
     */
    public function __construct(
        ProjectRepository $projectRepository,
        DocumentRepository $documentRepository,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        PageRepositoryInterface $pageRepository,
        PageByIdentifier $pageByIdentifier,
        BlockRepositoryInterface $blockRepository,
        BlockByIdentifier $blockByIdentifier,
        ConfigurationHelper $configurationHelper,
        ProjectHelper $projectHelper,
        StoreEmulation $storeEmulation,
        LoggerInterface $logger
    ) {
        $this->projectRepository = $projectRepository;
        $this->documentRepository = $documentRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pageRepository = $pageRepository;
        $this->pageByIdentifier = $pageByIdentifier;
        $this->blockRepository = $blockRepository;
        $this->blockByIdentifier = $blockByIdentifier;
        $this->configurationHelper = $configurationHelper;
        $this->projectHelper = $projectHelper;
        $this->storeEmulation = $storeEmulation;
        $this->logger = $logger;
    }

    /**
     * @param MessageInterface $message message
     *
     * @return void
     * @throws LocalizedException
     */
    public function processMessage(MessageInterface $message)
    {
        $noSuchEntity = false;
        $result = true;
        $document = false;
        try {
            $project = $this->projectRepository->getById($message->getProjectId());
            $document = $this->documentRepository->getById($message->getDocumentId());

            $documentResponse = $this->configurationHelper->getConnectorHelper()->getDocuments(
                $project->getTextMasterId(),
                $document->getTextMasterId()
            );

            if (isset($documentResponse['downloadUrl'])) {
                $result = $this->translationProcess(
                    $documentResponse['downloadUrl'],
                    $project,
                    $document
                );

            }
        } catch (NoSuchEntityException $noSuchEntityException) {
            $noSuchEntity = true;
            $this->logger->warning($noSuchEntityException->getMessage());
        } catch (LocalizedException $localizedException) {
            $result = false;
            $this->logger->warning($localizedException->getMessage());
        }

        if (!$noSuchEntity && !$result && $document) {
            $document->setErrorMessage((string)__('Apply translation Error'));
            $this->documentRepository->save($document);
        }
    }

    /**
     * Process of translation application into Magento getting translated elements from API
     *
     * @param array $DocumentDownloadUrl
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     *
     * @return bool
     */
    public function translationProcess(
        array $DocumentDownloadUrl,
        ProjectInterface $project,
        DocumentInterface $document
    ): bool {
        $translatedContent = [];
        $error = false;

        foreach ($DocumentDownloadUrl as $key => $value) {
            $translatedContent[$key] = $this->projectHelper->formatTranslatedText($value);
        }

        if (!empty($translatedContent)) {
            $this->storeEmulation->startEnvironmentEmulation(
                $project->getTargetStoreId(),
                Area::AREA_FRONTEND,
                true
            );

            $result = $this->applyTranslation($translatedContent, $project, $document);

            if ($result === true) {
                $this->projectHelper->setIsAppliedDocument($document);
                $this->acceptDocument($project, $document);
            } else {
                $error = true;
            }

            $this->storeEmulation->stopEnvironmentEmulation();
        }
        return !$error;
    }

    /**
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     */
    public function acceptDocument(ProjectInterface $project, DocumentInterface $document)
    {
        try {
            $this->configurationHelper->getConnectorHelper()->acceptDocument(
                $project->getTextMasterId(),
                $document->getTextMasterId(),
                AcceptDocument::STATUS_ACCEPTED
            );
        } catch (LocalizedException $e) {
            // acceptDocument api call failed, maybe document is already accepted
            // since document can still be "in_review" status
            // if document callback has not been called to change status in M2
            // problem : we don't know if a document is already accepted
            $this->logger->warning($e->getMessage());
        }
    }

    /**
     * @param array $translatedContent
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     *
     * @return bool
     */
    public function applyTranslation(
        array $translatedContent,
        ProjectInterface $project,
        DocumentInterface $document
    ): bool {
        $result = false;
        if ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_PRODUCT) {
            $result = $this->applyProductTranslation($translatedContent, $project, $document);
        } elseif ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_CATEGORY) {
            $result = $this->applyCategoryTranslation($translatedContent, $project, $document);
        } elseif ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_PAGE) {
            $result = $this->applyPageTranslation($translatedContent, $project, $document);
        } elseif ($project->getDocumentType() === TranslatableContentInterface::DOCUMENT_TYPE_BLOCK) {
            $result = $this->applyBlockTranslation($translatedContent, $project, $document);
        }
        return $result;
    }

    /**
     * @param array $translatedContent
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     *
     * @return bool
     */
    public function applyProductTranslation(
        array $translatedContent,
        ProjectInterface $project,
        DocumentInterface $document
    ): bool {
        try {
            $product = $this->productRepository->getById($document->getMagentoEntityId());
            $product->setStoreId($project->getTargetStoreId());
            foreach ($translatedContent as $key => $value) {
                $product->setData($key, $value);
                $product->getResource()->saveAttribute($product, $key);
            }
            return true;
        } catch (LocalizedException $e) {
            return false;
        }
    }

    /**
     * @param array $translatedContent
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     *
     * @return bool
     */
    public function applyCategoryTranslation(
        array $translatedContent,
        ProjectInterface $project,
        DocumentInterface $document
    ): bool {
        try {
            $category = $this->categoryRepository->get($document->getMagentoEntityId());
            $category->setStoreId($project->getTargetStoreId());
            foreach ($translatedContent as $key => $value) {
                $category->setData($key, $value);
            }

            $this->categoryRepository->save($category);
            return true;
        } catch (LocalizedException $e) {
            return false;
        }
    }

    /**
     * @param array $translatedContent
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @return bool
     */
    public function applyPageTranslation(
        array $translatedContent,
        ProjectInterface $project,
        DocumentInterface $document
    ): bool {
        try {
            $sourcePage = $this->pageRepository->getById($document->getMagentoEntityId());
        } catch (LocalizedException $e) {
            return false;
        }

        // if cms page with project source store id doesnt exist, we create a new cms page with source store id
        // mandatory to save cms page on target store id
        $sourcePageExist = true;
        try {
            $sourcePage = $this->pageByIdentifier->execute($sourcePage->getIdentifier(), $project->getSourceStoreId());
            $sourceStoreId = $sourcePage->getStoreId();
            if ($sourceStoreId[0] != $project->getSourceStoreId() || count($sourceStoreId) > 1) {
                $sourcePageExist = false;
            }
        } catch (NoSuchEntityException $e) {
            $sourcePageExist = false;
        }
        if (!$sourcePageExist) {
            $sourcePage->setData('store_id', [$project->getSourceStoreId()]);
            try {
                $this->pageRepository->save($sourcePage);
            } catch (LocalizedException $e) {
                return false;
            }
        }

        // if cms page with project target id exists, we load it to update it (reapplication of translation).
        // Otherwise, we create a new cms page with target store id and same identifier
        $targetPageExist = true;
        try {
            $targetPage = $this->pageByIdentifier->execute($sourcePage->getIdentifier(), $project->getTargetStoreId());
            $targetStoreId = $targetPage->getStoreId();
            if ($targetStoreId[0] != $project->getTargetStoreId() || count($targetStoreId) > 1) {
                $targetPageExist = false;
            }
        } catch (NoSuchEntityException $e) {
            $targetPageExist = false;
        }
        if (!$targetPageExist) {
            $targetPage = clone $sourcePage;
            $targetPage->setId(null);
            $targetPage->setData(PageInterface::CREATION_TIME, null);
            $targetPage->setData('store_id', [$project->getTargetStoreId()]);
        }

        // application of translated content on cms page attributes
        foreach ($translatedContent as $key => $value) {
            $targetPage->setData($key, $value);
        }

        try {
            $this->pageRepository->save($targetPage);
            return true;
        } catch (LocalizedException $e) {
            return false;
        }
    }

    /**
     * @param array $translatedContent
     * @param ProjectInterface $project
     * @param DocumentInterface $document
     * @return bool
     */
    public function applyBlockTranslation(
        array $translatedContent,
        ProjectInterface $project,
        DocumentInterface $document
    ): bool {
        try {
            $sourceBlock = $this->blockRepository->getById($document->getMagentoEntityId());
        } catch (LocalizedException $e) {
            return false;
        }

        // if cms block with project source store id doesnt exist, we create a new cms block with source store id
        // mandatory to save cms block on target store id
        $sourceBlockExist = true;
        try {
            $sourceBlock = $this->blockByIdentifier->execute(
                $sourceBlock->getIdentifier(),
                $project->getSourceStoreId()
            );
            $sourceStoreId = $sourceBlock->getStoreId();
            if ($sourceStoreId[0] != $project->getSourceStoreId() || count($sourceStoreId) > 1) {
                $sourceBlockExist = false;
            }
        } catch (NoSuchEntityException $e) {
            $sourceBlockExist = false;
        }
        if (!$sourceBlockExist) {
            $sourceBlock->setData('store_id', [$project->getSourceStoreId()]);
            $sourceBlock->setData('stores', [$project->getSourceStoreId()]);
            try {
                $this->blockRepository->save($sourceBlock);
            } catch (LocalizedException $e) {
                return false;
            }
        }

        // if cms block with project target id exists, we load it to update it (reapplication of translation).
        // Otherwise, we create a new cms block with target store id and same identifier
        $targetBlockExist = true;
        try {
            $targetBlock = $this->blockByIdentifier->execute(
                $sourceBlock->getIdentifier(),
                $project->getTargetStoreId()
            );
            $targetStoreId = $targetBlock->getStoreId();
            if ($targetStoreId[0] != $project->getTargetStoreId() || count($targetStoreId) > 1) {
                $targetBlockExist = false;
            }
        } catch (NoSuchEntityException $e) {
            $targetBlockExist = false;
        }
        if (!$targetBlockExist) {
            $targetBlock = clone $sourceBlock;
            $targetBlock->setId(null);
            $targetBlock->setData(BlockInterface::CREATION_TIME, null);
            $targetBlock->setData('store_id', [$project->getTargetStoreId()]);
            $targetBlock->setData('stores', [$project->getTargetStoreId()]);
        }

        // application of translated content on cms block attributes
        foreach ($translatedContent as $key => $value) {
            $targetBlock->setData($key, $value);
        }

        try {
            $this->blockRepository->save($targetBlock);
            return true;
        } catch (LocalizedException $e) {
            return false;
        }
    }
}
