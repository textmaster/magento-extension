<?php
/**
 * Project Attribute Resource Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use TextMaster\TextMaster\Api\Data\ProjectAttributeInterface;

class ProjectAttribute extends AbstractDb
{
    /**
     * Magento Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            ProjectAttributeInterface::TABLE_NAME,
            ProjectAttributeInterface::FIELD_PROJECT_ATTRIBUTE_ID
        );
    }
}
