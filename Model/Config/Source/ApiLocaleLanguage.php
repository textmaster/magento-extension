<?php
/**
 * Api Locale Language Option Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;

class ApiLocaleLanguage implements OptionSourceInterface
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
        $options = [];
        foreach ($this->connectorHelper->getLanguages() as $language) {
            $options[$language['code']] = __($language['name']) . ' - ' . $language['code'];
        }
        return $options;
    }
}
