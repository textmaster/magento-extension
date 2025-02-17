<?php
/**
 * Document Resource Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use TextMaster\TextMaster\Api\Data\DocumentInterface;

class Document extends AbstractDb
{
    /**
     * Magento Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            DocumentInterface::TABLE_NAME,
            DocumentInterface::FIELD_DOCUMENT_ID
        );
    }
}
