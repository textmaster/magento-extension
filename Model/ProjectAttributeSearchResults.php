<?php
/**
 * Project Attribute Search Results Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\ProjectAttributeSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class ProjectAttributeSearchResults extends SearchResults implements ProjectAttributeSearchResultsInterface
{

}
