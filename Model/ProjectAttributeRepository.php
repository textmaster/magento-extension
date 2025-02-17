<?php
/**
 * Project Attribute Repository
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
use TextMaster\TextMaster\Api\Data\ProjectAttributeInterface;
use TextMaster\TextMaster\Api\Data\ProjectAttributeInterfaceFactory as ProjectAttributeFactory;
use TextMaster\TextMaster\Api\Data\ProjectAttributeSearchResultsInterface;
use TextMaster\TextMaster\Api\Data\ProjectAttributeSearchResultsInterfaceFactory;
use TextMaster\TextMaster\Api\ProjectAttributeRepositoryInterface;
use TextMaster\TextMaster\Model\ResourceModel\ProjectAttribute\Collection;
use TextMaster\TextMaster\Model\ResourceModel\ProjectAttribute\CollectionFactory;
use TextMaster\TextMaster\Model\ResourceModel\ProjectAttribute as ResourceProjectAttribute;

class ProjectAttributeRepository implements ProjectAttributeRepositoryInterface
{
    /**
     * @var ProjectAttributeFactory
     */
    protected $projectAttributeFactory;

    /**
     * @var ResourceProjectAttribute
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
     * @var ProjectAttributeSearchResultsInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * ProjectAttributeRepository constructor.
     *
     * @param ProjectAttributeFactory                       $projectAttributeFactory  ProjectAttribute Factory
     * @param ResourceProjectAttribute                      $resource                 Resource Project Attribute
     * @param CollectionFactory                             $collectionFactory        Collection Factory
     * @param SearchCriteriaInterfaceFactory                $searchCriteriaFactory    Criteria Factory
     * @param CollectionProcessorInterface                  $collectionProcessor      Collection Processor
     * @param ProjectAttributeSearchResultsInterfaceFactory $searchResultFactory      ProjectAttribute Search Results
     */
    public function __construct(
        ProjectAttributeFactory $projectAttributeFactory,
        ResourceProjectAttribute $resource,
        CollectionFactory $collectionFactory,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        CollectionProcessorInterface $collectionProcessor,
        ProjectAttributeSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->projectAttributeFactory = $projectAttributeFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $projectAttributeId): ProjectAttributeInterface
    {
        $projectAttribute = $this->projectAttributeFactory->create();
        $this->resource->load($projectAttribute, $projectAttributeId);
        if (!$projectAttribute->getProjectAttributeId()) {
            throw new NoSuchEntityException(__('Unable to find project attribute with ID "%1"', $projectAttributeId));
        }
        return $projectAttribute;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): ProjectAttributeSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $searchCriteria ?? $this->searchCriteriaFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProjectAttributeSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(ProjectAttributeInterface $projectAttribute): ProjectAttributeInterface
    {
        $this->resource->save($projectAttribute);
        return $projectAttribute;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $projectAttributeId): bool
    {
        $projectAttribute = $this->getById($projectAttributeId);
        return $this->delete($projectAttribute);
    }

    /**
     * @inheritDoc
     */
    public function delete(ProjectAttributeInterface $projectAttribute): bool
    {
        $this->resource->delete($projectAttribute);
        return true;
    }
}
