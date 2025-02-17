<?php
/**
 * Translatable Content Collection
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel\TranslatableContent;

use TextMaster\TextMaster\Model\TranslatableContent;
use TextMaster\TextMaster\Model\ResourceModel\TranslatableContent as TranslatableContentResourceModel;
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
            TranslatableContent::class,
            TranslatableContentResourceModel::class
        );
    }
}
