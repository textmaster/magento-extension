<?php
/**
 * Project Repository Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

use TextMaster\TextMaster\Api\Data\ProjectSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ProjectRepositoryInterface
{
    /**
     * Retrieve a project by its id
     *
     * @param int $projectId
     *
     * @return ProjectInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $projectId): ProjectInterface;

    /**
     * Retrieve a project by its textmaster_id
     *
     * @param string $textMasterId
     *
     * @return ProjectInterface
     * @throws NoSuchEntityException
     */
    public function getByTextMasterId(string $textMasterId): ProjectInterface;

    /**
     * Retrieve projects which match a specified criteria.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return ProjectSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): ProjectSearchResultsInterface;

    /**
     * save a project
     *
     * @param ProjectInterface $project
     *
     * @return ProjectInterface
     * @throws CouldNotSaveException
     */
    public function save(ProjectInterface $project): ProjectInterface;

    /**
     * Delete a project by its id
     *
     * @param int $projectId
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $projectId): bool;
}
