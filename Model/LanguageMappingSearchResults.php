<?php
/**
 * Language Mapping Search Results Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\LanguageMappingSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class LanguageMappingSearchResults extends SearchResults implements LanguageMappingSearchResultsInterface
{

}
