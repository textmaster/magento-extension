<?php
/**
 * Language Mapping Resource Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use TextMaster\TextMaster\Api\Data\LanguageMappingInterface;

class LanguageMapping extends AbstractDb
{
    /**
     * Magento Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            LanguageMappingInterface::TABLE_NAME,
            LanguageMappingInterface::FIELD_LANGUAGE_MAPPING_ID
        );
    }
}
