<?php
/**
 * Language Mapping Collection
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\ResourceModel\LanguageMapping;

use TextMaster\TextMaster\Model\LanguageMapping;
use TextMaster\TextMaster\Model\ResourceModel\LanguageMapping as LanguageMappingResourceModel;
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
            LanguageMapping::class,
            LanguageMappingResourceModel::class
        );
    }
}
