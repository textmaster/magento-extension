<?php
/**
 * Project Search Results Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\ProjectSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class ProjectSearchResults extends SearchResults implements ProjectSearchResultsInterface
{

}
