<?php
/**
 * Project Attribute Collection
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel\ProjectAttribute;

use TextMaster\TextMaster\Model\ProjectAttribute;
use TextMaster\TextMaster\Model\ResourceModel\ProjectAttribute as ProjectAttributeResourceModel;
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
            ProjectAttribute::class,
            ProjectAttributeResourceModel::class
        );
    }
}
