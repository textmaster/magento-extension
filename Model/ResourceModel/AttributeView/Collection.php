<?php
/**
 * Attribute View Collection
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel\AttributeView;

use TextMaster\TextMaster\Model\AttributeView;
use TextMaster\TextMaster\Model\ResourceModel\AttributeView as AttributeViewResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attribute_view_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            AttributeView::class,
            AttributeViewResourceModel::class
        );
    }
}
