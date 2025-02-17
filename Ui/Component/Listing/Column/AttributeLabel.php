<?php
/**
 * Class AttributeLabel
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class AttributeLabel extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $label = '';
                if (isset($item['document_type']) && isset($item['attribute_label'])) {
                    $label = $item['attribute_label'];
                    if (in_array($item['document_type'], ['page', 'block'])) {
                        $label = __($label);
                    }
                }
                $item[$this->getData('name')] = $label . ' (' . $item[$this->getData('name')] . ')';
            }
        }

        return $dataSource;
    }
}
