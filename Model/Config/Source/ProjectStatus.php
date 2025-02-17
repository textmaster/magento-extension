<?php
/**
 * Project Status Option Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;

class ProjectStatus implements OptionSourceInterface
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
            ProjectInterface::STATUS_IN_CREATION => __('In creation'),
            ProjectInterface::STATUS_IN_PROGRESS => __('In progress'),
            ProjectInterface::STATUS_IN_REVIEW => __('In review'),
            ProjectInterface::STATUS_COMPLETED => __('Completed'),
            ProjectInterface::STATUS_PAUSED => __('Paused'),
            ProjectInterface::STATUS_CANCELED => __('Canceled')
        ];
    }
}
