<?php
/**
 * Project Repository
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
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterfaceFactory as ProjectFactory;
use TextMaster\TextMaster\Api\Data\ProjectSearchResultsInterface;
use TextMaster\TextMaster\Api\Data\ProjectSearchResultsInterfaceFactory;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface;
use TextMaster\TextMaster\Model\ResourceModel\Project as ResourceProject;
use TextMaster\TextMaster\Model\ResourceModel\Project\Collection;
use TextMaster\TextMaster\Model\ResourceModel\Project\CollectionFactory;

class ProjectRepository implements ProjectRepositoryInterface
{
    /** @var ProjectFactory */
    protected $projectFactory;
    /** @var ResourceProject */
    protected $resource;
    /** @var CollectionFactory */
    protected $collectionFactory;
    /** @var SearchCriteriaInterfaceFactory */
    protected $searchCriteriaFactory;
    /** @var CollectionProcessorInterface */
    protected $collectionProcessor;
    /** @var ProjectSearchResultsInterfaceFactory */
    protected $searchResultFactory;

    /**
     * ProjectRepository constructor.
     *
     * @param ProjectFactory                       $projectFactory        Project Factory
     * @param ResourceProject                      $resource              Resource Project
     * @param CollectionFactory                    $collectionFactory     Collection Factory
     * @param SearchCriteriaInterfaceFactory       $searchCriteriaFactory Criteria Factory
     * @param CollectionProcessorInterface         $collectionProcessor   Collection Processor
     * @param ProjectSearchResultsInterfaceFactory $searchResultFactory   Project Search Results
     */
    public function __construct(
        ProjectFactory $projectFactory,
        ResourceProject $resource,
        CollectionFactory $collectionFactory,
        SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        CollectionProcessorInterface $collectionProcessor,
        ProjectSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->projectFactory = $projectFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $projectId): ProjectInterface
    {
        $project = $this->projectFactory->create();
        $this->resource->load($project, $projectId);
        if (!$project->getId()) {
            throw new NoSuchEntityException(__('Unable to find project with ID "%1"', $projectId));
        }
        return $project;
    }

    /**
     * @inheritDoc
     */
    public function getByTextMasterId(string $textMasterId): ProjectInterface
    {
        $project = $this->projectFactory->create();
        $this->resource->load($project, $textMasterId, ProjectInterface::FIELD_TEXTMASTER_ID);
        if (!$project->getTextMasterId()) {
            throw new NoSuchEntityException(__('Unable to find project with TextMaster ID "%1"', $textMasterId));
        }
        return $project;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): ProjectSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $searchCriteria ?? $this->searchCriteriaFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProjectSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(ProjectInterface $project): ProjectInterface
    {
        $this->resource->save($project);
        return $project;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $projectId): bool
    {
        $project = $this->getById($projectId);
        return $this->delete($project);
    }

    /**
     * @inheritDoc
     */
    public function delete(ProjectInterface $project): bool
    {
        $this->resource->delete($project);
        return true;
    }
}
