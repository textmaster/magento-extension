<?php
/**
 * Project Search result Data Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ProjectSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get project list
     *
     * @return ProjectInterface[]
     */
    public function getItems();

    /**
     * Set project list
     *
     * @param ProjectInterface[] $items list of projects
     *
     * @return $this
     */
    public function setItems(array $items);
}
