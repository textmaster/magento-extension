<?php
/**
 * Language Mapping Repository Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

use TextMaster\TextMaster\Api\Data\LanguageMappingInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use TextMaster\TextMaster\Api\Data\LanguageMappingSearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;

/**
 * @api
 */
interface LanguageMappingRepositoryInterface
{
    /**
     * Create languagemapping service
     *
     * @param LanguageMappingInterface $languageMapping
     * @return LanguageMappingInterface
     * @throws CouldNotSaveException
     */
    public function save(LanguageMappingInterface $languageMapping): LanguageMappingInterface;

    /**
     * Get info about language mapping by languagemapping id
     *
     * @param int $languageMappingId
     * @return LanguageMappingInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $languageMappingId): LanguageMappingInterface;

    /**
     * Retrieve language mapping which match a specified criteria.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return LanguageMappingSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): LanguageMappingSearchResultsInterface;

    /**
     * Delete languagemapping by identifier
     *
     * @param LanguageMappingInterface $languageMapping $languageMapping which will deleted
     * @return bool Will returned True if deleted
     * @throws InputException
     * @throws StateException
     * @throws NoSuchEntityException
     */
    public function delete(LanguageMappingInterface $languageMapping): bool;

    /**
     * Delete languagemapping by identifier
     *
     * @param int $languageMappingId
     * @return bool Will returned True if deleted
     * @throws InputException
     * @throws StateException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $languageMappingId): bool;
}
