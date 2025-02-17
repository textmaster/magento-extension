<?php
/**
 * Language Mapping Search Results Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface LanguageMappingSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get language mapping list
     *
     * @return LanguageMappingInterface[]
     */
    public function getItems();

    /**
     * Set language mapping list
     *
     * @param LanguageMappingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
