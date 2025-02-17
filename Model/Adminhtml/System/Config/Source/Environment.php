<?php
/**
 * Environment Options Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Adminhtml\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Environment implements OptionSourceInterface
{
    /**
     * Staging DocumentType
     */
    const ENV_STAGING = 'staging';

    /**
     * Production DocumentType
     */
    const ENV_PROD = 'production';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ENV_STAGING,
                'label' => __('textmaster_staging')
            ],
            [
                'value' => self::ENV_PROD,
                'label' => __('textmaster_production')
            ]
        ];
    }
}
