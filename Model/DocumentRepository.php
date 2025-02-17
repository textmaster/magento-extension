<?php
/**
 * Document Repository
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\DocumentInterfaceFactory as DocumentFactory;
use TextMaster\TextMaster\Api\Data\DocumentSearchResultsInterface;
use TextMaster\TextMaster\Api\Data\DocumentSearchResultsInterfaceFactory;
use TextMaster\TextMaster\Api\DocumentRepositoryInterface;
use TextMaster\TextMaster\Model\ResourceModel\Document\Collection;
use TextMaster\TextMaster\Model\ResourceModel\Document\CollectionFactory;
use TextMaster\TextMaster\Model\ResourceModel\Document as ResourceDocument;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * @var DocumentFactory
     */
    protected $documentFactory;

    /**
     * @var ResourceDocument
     */
    protected $resource;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SearchCriteriaInterfaceFactory
     */
    protected $searchCriteriaFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var DocumentSearchResultsInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * DocumentRepository constructor.
     *
     * @param DocumentFactory                       $documentFactory       Document Factory
     * @param ResourceDocument                      $resource              Resource Document
     * @param CollectionFactory                     $collectionFactory     Collection Factory
     * @param SearchCriteriaInterfaceFactory        $searchCriteriaFactory Criteria Factory
     * @param CollectionProcessorInterface          $collectionProcessor   Collection Processor
     * @param DocumentSearchResultsInterfaceFactory $searchResultFactory   Document Search Results
     */
    public function __construct(
        DocumentFactory $documentFactory,
        ResourceDocument $resource,
        CollectionFactory $collectionFactory,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        CollectionProcessorInterface $collectionProcessor,
        DocumentSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->documentFactory = $documentFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $documentId): DocumentInterface
    {
        $document = $this->documentFactory->create();
        $this->resource->load($document, $documentId);
        if (!$document->getDocumentId()) {
            throw new NoSuchEntityException(__('Unable to find document with ID "%1"', $documentId));
        }
        return $document;
    }

    /**
     * @inheritDoc
     */
    public function getByTextMasterId(string $textMasterId): DocumentInterface
    {
        $document = $this->documentFactory->create();
        $this->resource->load($document, $textMasterId, DocumentInterface::FIELD_TEXTMASTER_ID);
        if (!$document->getTextMasterId()) {
            throw new NoSuchEntityException(__('Unable to find document with TextMaster ID "%1"', $textMasterId));
        }
        return $document;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): DocumentSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $searchCriteria ?? $this->searchCriteriaFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var DocumentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(DocumentInterface $document): DocumentInterface
    {
        $this->resource->save($document);
        return $document;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $documentId): bool
    {
        $document = $this->getById($documentId);
        return $this->delete($document);
    }

    /**
     * @inheritDoc
     */
    public function delete(DocumentInterface $document): bool
    {
        $this->resource->delete($document);
        return true;
    }
}
