<?php
/**
 * TextMaster Project helper
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Helper;

use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\DocumentSearchResultsInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Api\Data\ProjectSearchResultsInterface;
use TextMaster\TextMaster\Api\DocumentRepositoryInterface as DocumentRepository;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface as ProjectRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Project
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DocumentRepository
     */
    protected $documentRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * Project constructor.
     * @param ProjectRepository $projectRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DocumentRepository $documentRepository
     * @param LoggerInterface $logger
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        ProjectRepository $projectRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DocumentRepository $documentRepository,
        LoggerInterface $logger,
        TimezoneInterface $timezoneInterface
    ) {
        $this->projectRepository = $projectRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->documentRepository = $documentRepository;
        $this->logger = $logger;
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * Check all documents of a project are in status "completed" to complete project
     *
     * @param ProjectInterface $project
     */
    public function completeProject(ProjectInterface $project)
    {
        if (count($this->getCompletedDocuments($project)->getItems()) ===
            count($this->getAllDocuments($project)->getItems())
        ) {
            if (count($this->getTranslatedDocuments($project)->getItems()) ===
                count($this->getAllDocuments($project)->getItems())) {
                $project->setIsApplied(true);
            }
            $project->setStatus(ProjectInterface::STATUS_COMPLETED);
            try {
                $this->projectRepository->save($project);
            } catch (CouldNotSaveException $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * @param ProjectInterface $project
     */
    public function updateProjectStatusInReview(ProjectInterface $project)
    {
        if (count($this->getInReviewDocuments($project)->getItems()) ===
            count($this->getAllDocuments($project)->getItems())
        ) {
            $project->setStatus(ProjectInterface::STATUS_IN_REVIEW);
            try {
                $this->projectRepository->save($project);
            } catch (CouldNotSaveException $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * Check if all documents are on the same status and then update project status
     * Useful when document callback is called to check if new status set on a document is also set on all the documents
     *
     * @param DocumentInterface $document
     */
    public function updateProjectStatus(DocumentInterface $document)
    {
        try {
            $project = $this->projectRepository->getById($document->getProjectId());

            if ($document->getStatus() === DocumentInterface::STATUS_IN_REVIEW) {
                $this->updateProjectStatusInReview($project);
            } elseif ($document->getStatus() === DocumentInterface::STATUS_COMPLETED) {
                $this->completeProject($project);
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param ProjectInterface $project
     * @return DocumentSearchResultsInterface
     */
    public function getCompletedDocuments(ProjectInterface $project): DocumentSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_STATUS,
            DocumentInterface::STATUS_COMPLETED,
            'eq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * @param ProjectInterface $project
     * @return DocumentSearchResultsInterface
     */
    public function getInReviewDocuments(ProjectInterface $project): DocumentSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_STATUS,
            DocumentInterface::STATUS_IN_REVIEW,
            'eq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * @param ProjectInterface $project
     * @return DocumentSearchResultsInterface
     */
    public function getAllDocuments(ProjectInterface $project): DocumentSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * Get Document list eligible to translation (in_review)
     *
     * @param ProjectInterface $project
     * @param array $selectedDocumentIds
     *
     * @return DocumentSearchResultsInterface
     */
    public function getTranslatableDocuments(
        ProjectInterface $project,
        array $selectedDocumentIds = []
    ): DocumentSearchResultsInterface {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_STATUS,
            DocumentInterface::STATUS_IN_REVIEW,
            'eq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        if (!empty($selectedDocumentIds)) {
            $this->searchCriteriaBuilder->addFilter(
                DocumentInterface::FIELD_DOCUMENT_ID,
                $selectedDocumentIds,
                'in'
            );
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * @param ProjectInterface $project
     * @return DocumentSearchResultsInterface
     */
    public function getTranslatedDocuments(ProjectInterface $project): DocumentSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_IS_APPLIED,
            true,
            'eq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * @return DocumentSearchResultsInterface
     */
    public function getExpiredDocuments(ProjectInterface $project): DocumentSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_START_TRANSLATION_AT,
            true,
            'notnull'
        );

        // if start_translation_at date < now - 2hours, something probably went wrong
        $maxEstimatedTimeForDocumentTranslation = $this->timezoneInterface
            ->date(new \DateTime())
            ->modify('-2 hours')
            ->format('Y-m-d H:i:s');

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_START_TRANSLATION_AT,
            $maxEstimatedTimeForDocumentTranslation,
            'lteq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_IS_APPLIED,
            0,
            'eq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * @param ProjectInterface $project
     * @return DocumentSearchResultsInterface
     */
    public function getBeingTranslatedDocuments(ProjectInterface $project): DocumentSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_START_TRANSLATION_AT,
            true,
            'notnull'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_IS_APPLIED,
            0,
            'eq'
        );

        $this->searchCriteriaBuilder->addFilter(
            DocumentInterface::FIELD_PROJECT_ID,
            $project->getProjectId(),
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->documentRepository->getList($searchCriteria);
    }

    /**
     * Return projects being translated (translation has started but not yet completely applied)
     *
     * @return ProjectSearchResultsInterface
     */
    public function getBeingTranslatedProjects(): ProjectSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            ProjectInterface::FIELD_START_TRANSLATION_AT,
            true,
            'notnull'
        );

        $this->searchCriteriaBuilder->addFilter(
            ProjectInterface::FIELD_IS_APPLIED,
            0,
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->projectRepository->getList($searchCriteria);
    }

    /**
     *
     * @param ProjectInterface $project
     * @return bool
     */
    public function isBeingTranslatedProject(ProjectInterface $project): bool
    {
        return count($this->getBeingTranslatedDocuments($project)->getItems()) > 0;
    }

    /**
     * @param int $projectId
     * @return ProjectInterface
     * @throws NoSuchEntityException
     */
    public function getProjectById(int $projectId): ProjectInterface
    {
        return $this->projectRepository->getById($projectId);
    }

    /**
     * If cron is called between last document translation application and before API changes status
     * we check that all documents are translated
     * Or we just set is_applied to true on project if all documents are is_applied
     *
     * @param ProjectInterface $project
     */
    public function setIsAppliedProject(ProjectInterface $project)
    {
        if (count($this->getTranslatedDocuments($project)->getItems()) ===
            count($this->getAllDocuments($project)->getItems())) {
            try {
                $project->setIsApplied(true);
                $this->projectRepository->save($project);
            } catch (CouldNotSaveException $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * @param ProjectInterface $project
     */
    public function setProjectStartTranslationAt(ProjectInterface $project)
    {
        $now = $this->timezoneInterface
            ->date(new \DateTime())
            ->format('Y-m-d H:i:s');

        try {
            $project->setStartTranslationAt($now);
            $this->projectRepository->save($project);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param DocumentInterface $document
     */
    public function setDocumentStartTranslationAt(DocumentInterface $document)
    {
        $now = $this->timezoneInterface
            ->date(new \DateTime())
            ->format('Y-m-d H:i:s');

        try {
            $document->setStartTranslationAt($now);
            $this->documentRepository->save($document);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param DocumentInterface $document
     */
    public function setIsAppliedDocument(DocumentInterface $document)
    {
        try {
            $document->setIsApplied(true);
            $this->documentRepository->save($document);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $project
     */
    public function resetProject($project)
    {
        try {
            $project->setStartTranslationAt(null);
            $this->projectRepository->save($project);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $document
     */
    public function resetDocument($document)
    {
        try {
            $document->setStartTranslationAt(null);
            $this->documentRepository->save($document);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param ProjectInterface $project
     */
    public function resetExpiredProject(ProjectInterface $project)
    {
        $this->setIsAppliedProject($project);
        $expiredDocuments = $this->getExpiredDocuments($project)->getItems();
        $hasExpiredDocuments = count($expiredDocuments) >= 1;

        if ($hasExpiredDocuments === true) {
            $this->resetProject($project);
            foreach ($expiredDocuments as $expiredDocument) {
                $this->resetDocument($expiredDocument);
            }
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public function formatTranslatedText(string $text): string
    {
        return $text;
    }
}
