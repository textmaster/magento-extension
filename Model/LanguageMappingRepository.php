<?php
/**
 * LanguageMapping Repository
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
use TextMaster\TextMaster\Api\Data\LanguageMappingInterface;
use TextMaster\TextMaster\Api\Data\LanguageMappingInterfaceFactory as LanguageMappingFactory;
use TextMaster\TextMaster\Api\Data\LanguageMappingSearchResultsInterface;
use TextMaster\TextMaster\Api\Data\LanguageMappingSearchResultsInterfaceFactory;
use TextMaster\TextMaster\Api\LanguageMappingRepositoryInterface;
use TextMaster\TextMaster\Model\ResourceModel\LanguageMapping\Collection;
use TextMaster\TextMaster\Model\ResourceModel\LanguageMapping\CollectionFactory;
use TextMaster\TextMaster\Model\ResourceModel\LanguageMapping as ResourceLanguageMapping;

class LanguageMappingRepository implements LanguageMappingRepositoryInterface
{
    /**
     * @var LanguageMappingFactory
     */
    protected $languageMappingFactory;

    /**
     * @var ResourceLanguageMapping
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
     * @var LanguageMappingSearchResultsInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * LanguageMappingRepository constructor.
     *
     * @param LanguageMappingFactory                       $languageMappingFactory LanguageMapping Factory
     * @param ResourceLanguageMapping                      $resource               Resource Language Mapping
     * @param CollectionFactory                            $collectionFactory      Collection Factory
     * @param SearchCriteriaInterfaceFactory               $searchCriteriaFactory  Criteria Factory
     * @param CollectionProcessorInterface                 $collectionProcessor    Collection Processor
     * @param LanguageMappingSearchResultsInterfaceFactory $searchResultFactory    LanguageMapping Search Results
     */
    public function __construct(
        LanguageMappingFactory $languageMappingFactory,
        ResourceLanguageMapping $resource,
        CollectionFactory $collectionFactory,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        CollectionProcessorInterface $collectionProcessor,
        LanguageMappingSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->languageMappingFactory = $languageMappingFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $languageMappingId): LanguageMappingInterface
    {
        $languageMapping = $this->languageMappingFactory->create();
        $this->resource->load($languageMapping, $languageMappingId);
        if (!$languageMapping->getLanguageMappingId()) {
            throw new NoSuchEntityException(__('Unable to find language mapping with ID "%1"', $languageMappingId));
        }
        return $languageMapping;
    }

    /**
     * @inheritDoc
     */
    public function getByTextMasterId(string $textMasterId): LanguageMappingInterface
    {
        $languageMapping = $this->languageMappingFactory->create();
        $this->resource->load($languageMapping, $textMasterId);
        if (!$languageMapping->getLanguageMappingId()) {
            throw new NoSuchEntityException(
                __('Unable to find language mapping with TextMaster ID "%1"', $textMasterId)
            );
        }
        return $languageMapping;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): LanguageMappingSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $searchCriteria ?? $this->searchCriteriaFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var LanguageMappingSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(LanguageMappingInterface $languageMapping): LanguageMappingInterface
    {
        $this->resource->save($languageMapping);
        return $languageMapping;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $languageMappingId): bool
    {
        $languageMapping = $this->getById($languageMappingId);
        return $this->delete($languageMapping);
    }

    /**
     * @inheritDoc
     */
    public function delete(LanguageMappingInterface $languageMapping): bool
    {
        $this->resource->delete($languageMapping);
        return true;
    }
}
