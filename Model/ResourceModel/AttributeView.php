<?php
/**
 * Attribute View Resource Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use TextMaster\TextMaster\Api\Data\AttributeViewInterface;

class AttributeView extends AbstractDb
{
    /**
     * Magento Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            AttributeViewInterface::TABLE_NAME,
            AttributeViewInterface::FIELD_ATTRIBUTE_VIEW_ID
        );
    }
}
