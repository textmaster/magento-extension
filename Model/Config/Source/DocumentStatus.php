<?php
/**
 * Document Status Option Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use TextMaster\TextMaster\Api\Data\DocumentInterface;

class DocumentStatus implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            DocumentInterface::STATUS_IN_CREATION => __('In creation'),
            DocumentInterface::STATUS_WAITING_ASSIGNMENT => __('Waiting assignment'),
            DocumentInterface::STATUS_IN_PROGRESS => __('In progress'),
            DocumentInterface::STATUS_IN_REVIEW => __('In review'),
            DocumentInterface::STATUS_COMPLETED => __('Completed'),
            DocumentInterface::STATUS_INCOMPLETE => __('Incomplete'),
            DocumentInterface::STATUS_PAUSED => __('Paused'),
            DocumentInterface::STATUS_CANCELED => __('Canceled'),
            DocumentInterface::STATUS_COPYSCAPE => __('Copyscape'),
            DocumentInterface::STATUS_COUNTING_WORDS => __('Counting words'),
            DocumentInterface::STATUS_QUALITY_CONTROL => __('Quality control')
        ];
    }
}
