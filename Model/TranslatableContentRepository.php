<?php
/**
 * Translatable Content Repository
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
use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterfaceFactory as TranslatableContentFactory;
use TextMaster\TextMaster\Api\Data\TranslatableContentSearchResultsInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentSearchResultsInterfaceFactory;
use TextMaster\TextMaster\Api\TranslatableContentRepositoryInterface;
use TextMaster\TextMaster\Model\ResourceModel\TranslatableContent\Collection;
use TextMaster\TextMaster\Model\ResourceModel\TranslatableContent\CollectionFactory;
use TextMaster\TextMaster\Model\ResourceModel\TranslatableContent as ResourceTranslatableContent;

class TranslatableContentRepository implements TranslatableContentRepositoryInterface
{
    /** @var TranslatableContentFactory */
    protected $translatableContentFactory;
    /** @var ResourceTranslatableContent */
    protected $resource;
    /** @var CollectionFactory */
    protected $collectionFactory;
    /** @var SearchCriteriaInterfaceFactory */
    protected $searchCriteriaFactory;
    /** @var CollectionProcessorInterface */
    protected $collectionProcessor;
    /** @var TranslatableContentSearchResultsInterfaceFactory */
    protected $searchResultFactory;

    /**
     * TranslatableContentRepository constructor.
     *
     * @param TranslatableContentFactory                       $translatableContentFactory TranslatableContent Factory
     * @param ResourceTranslatableContent                      $resource                   Resource Translatable Content
     * @param CollectionFactory                                $collectionFactory          Collection Factory
     * @param SearchCriteriaInterfaceFactory                   $searchCriteriaFactory      Criteria Factory
     * @param CollectionProcessorInterface                     $collectionProcessor        Collection Processor
     * @param TranslatableContentSearchResultsInterfaceFactory $searchResultFactory        Search Results Factory
     */
    public function __construct(
        TranslatableContentFactory $translatableContentFactory,
        ResourceTranslatableContent $resource,
        CollectionFactory $collectionFactory,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        CollectionProcessorInterface $collectionProcessor,
        TranslatableContentSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->translatableContentFactory = $translatableContentFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $translatableContentId): TranslatableContentInterface
    {
        $translatableContent = $this->translatableContentFactory->create();
        $this->resource->load($translatableContent, $translatableContentId);
        if (!$translatableContent->getTranslatableContentId()) {
            throw new NoSuchEntityException(
                __(
                    'Unable to find translatable content with ID "%1"',
                    $translatableContentId
                )
            );
        }
        return $translatableContent;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $searchCriteria ?? $this->searchCriteriaFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var TranslatableContentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(TranslatableContentInterface $translatableContent): TranslatableContentInterface
    {
        $this->resource->save($translatableContent);
        return $translatableContent;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $translatableContentId): bool
    {
        $translatableContent = $this->getById($translatableContentId);
        return $this->delete($translatableContent);
    }

    /**
     * @inheritDoc
     */
    public function delete(TranslatableContentInterface $translatableContent): bool
    {
        $this->resource->delete($translatableContent);
        return true;
    }
}
