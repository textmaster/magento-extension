<?php
/**
 * Document Collection
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel\Document;

use TextMaster\TextMaster\Model\Document;
use TextMaster\TextMaster\Model\ResourceModel\Document as DocumentResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'document_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Document::class,
            DocumentResourceModel::class
        );
    }
}
