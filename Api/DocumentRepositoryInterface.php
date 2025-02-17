<?php
/**
 * Document Repository Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api;

use TextMaster\TextMaster\Api\Data\DocumentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use TextMaster\TextMaster\Api\Data\DocumentInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface DocumentRepositoryInterface
{
    /**
     * Retrieve a document by its id
     *
     * @param int $documentId
     *
     * @return DocumentInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $documentId): DocumentInterface;

    /**
     * Retrieve a document by its textmaster_id
     *
     * @param string $textMasterId
     *
     * @return DocumentInterface
     * @throws NoSuchEntityException
     */
    public function getByTextMasterId(string $textMasterId): DocumentInterface;

    /**
     * Retrieve documents which match a specified criteria.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return DocumentSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): DocumentSearchResultsInterface;

    /**
     * save a document
     *
     * @param DocumentInterface $document
     *
     * @return DocumentInterface
     * @throws CouldNotSaveException
     */
    public function save(DocumentInterface $document): DocumentInterface;

    /**
     * Delete a document by its id
     *
     * @param int $documentId
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $documentId): bool;
}
