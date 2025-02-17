<?php
/**
 * Product Attribute Search result Data Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ProjectAttributeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get project attribute list
     *
     * @return ProjectAttributeInterface[]
     */
    public function getItems();

    /**
     * Set project attribute list
     *
     * @param ProjectAttributeInterface[] $items list of project attribute
     *
     * @return $this
     */
    public function setItems(array $items);
}
