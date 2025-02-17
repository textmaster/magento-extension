<?php
/**
 * Translatable Content Repository Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentSearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;

/**
 * @api
 */
interface TranslatableContentRepositoryInterface
{
    /**
     * Create translatable content service
     *
     * @param TranslatableContentInterface $translatableContent
     *
     * @return TranslatableContentInterface
     * @throws CouldNotSaveException
     */
    public function save(TranslatableContentInterface $translatableContent): TranslatableContentInterface;

    /**
     * Get info about translatable content by translatable content id
     *
     * @param int $translatableContentId
     *
     * @return TranslatableContentInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $translatableContentId): TranslatableContentInterface;

    /**
     * Retrieve translatable content which match a specified criteria.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return TranslatableContentSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * Delete translatable content by identifier
     *
     * @param TranslatableContentInterface $translatableContent $translatableContent which will deleted
     *
     * @return bool Will returned True if deleted
     * @throws InputException
     * @throws StateException
     * @throws NoSuchEntityException
     */
    public function delete(TranslatableContentInterface $translatableContent): bool;

    /**
     * Delete translatable content by identifier
     *
     * @param int $translatableContentId
     *
     * @return bool Will returned True if deleted
     * @throws InputException
     * @throws StateException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $translatableContentId): bool;
}
