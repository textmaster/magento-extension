<?php
/**
 * Document Search result Data Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DocumentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get document list
     *
     * @return DocumentInterface[]
     */
    public function getItems();

    /**
     * Set document list
     *
     * @param DocumentInterface[] $items list of documents
     *
     * @return $this
     */
    public function setItems(array $items);
}
