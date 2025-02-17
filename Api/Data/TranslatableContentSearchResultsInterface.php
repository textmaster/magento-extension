<?php
/**
 * Translatable Content Search Results Interface
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
interface TranslatableContentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get translatable content list
     *
     * @return TranslatableContentInterface[]
     */
    public function getItems(): array;

    /**
     * Set translatable content list
     *
     * @param TranslatableContentInterface[] $items
     *
     * @return TranslatableContentSearchResultsInterface
     */
    public function setItems(array $items): TranslatableContentSearchResultsInterface;
}
