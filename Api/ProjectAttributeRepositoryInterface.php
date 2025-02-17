<?php
/**
 * Project Attribute Repository Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

use TextMaster\TextMaster\Api\Data\ProjectAttributeSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use TextMaster\TextMaster\Api\Data\ProjectAttributeInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ProjectAttributeRepositoryInterface
{
    /**
     * Retrieve a project attribute by its id
     *
     * @param int $projectAttributeId
     *
     * @return ProjectAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $projectAttributeId): ProjectAttributeInterface;

    /**
     * Retrieve project attribute which match a specified criteria.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return ProjectAttributeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): ProjectAttributeSearchResultsInterface;

    /**
     * save a project attribute
     *
     * @param ProjectAttributeInterface $projectAttribute
     *
     * @return ProjectAttributeInterface
     * @throws CouldNotSaveException
     */
    public function save(ProjectAttributeInterface $projectAttribute): ProjectAttributeInterface;

    /**
     * Delete a project attribute by its id
     *
     * @param int $projectAttributeId
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $projectAttributeId): bool;
}
