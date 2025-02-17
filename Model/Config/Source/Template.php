<?php
/**
 * Template Option Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;
use Magento\Framework\Exception\LocalizedException;

class Template implements OptionSourceInterface
{
    /**
     * @var ConnectorHelper
     */
    protected $connectorHelper;

    /**
     * Constructor
     *
     * @param ConnectorHelper $connectorHelper
     */
    public function __construct(
        ConnectorHelper $connectorHelper
    ) {
        $this->connectorHelper = $connectorHelper;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $result;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getOptions(): array
    {
        $options = [];
        foreach ($this->connectorHelper->getTemplates() as $template) {
            $options[$template['id']] = (string)__($template['name']);
        }
        return $options;
    }
}
