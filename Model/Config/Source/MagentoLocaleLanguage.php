<?php
/**
 * Language Mapping Source Language Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Locale\Config as LocaleConfig;

class MagentoLocaleLanguage implements OptionSourceInterface
{
    /**
     * @var LocaleConfig
     */
    protected $localeConfig;

    /**
     * MagentoLocaleLanguage constructor.
     * @param LocaleConfig $localeConfig
     */
    public function __construct(
        LocaleConfig $localeConfig
    ) {
        $this->localeConfig = $localeConfig;
    }

    public function toOptionArray()
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
    public function getOptions()
    {
        $countries = [];
        foreach ($this->localeConfig->getAllowedLocales() as $countryCode) {
            $countries[$countryCode] = $countryCode;
        }
        return $countries;
    }
}
