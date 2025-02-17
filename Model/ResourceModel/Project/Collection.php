<?php
/**
 * Project Collection
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel\Project;

use TextMaster\TextMaster\Model\Project;
use TextMaster\TextMaster\Model\ResourceModel\Project as ProjectResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Project::class,
            ProjectResourceModel::class
        );
    }
}
