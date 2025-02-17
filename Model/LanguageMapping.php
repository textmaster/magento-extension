<?php
/**
 * Language Mapping Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\LanguageMappingInterface;
use TextMaster\TextMaster\Model\ResourceModel\LanguageMapping as LanguageMappingResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class LanguageMapping extends AbstractModel implements LanguageMappingInterface, IdentityInterface
{
    /**
     * Language Mapping cache tag
     */
    const CACHE_TAG = 'textmaster_language_mapping';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Magento Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            LanguageMappingResourceModel::class
        );
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getLanguageMappingId(): ?int
    {
        return (int) $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getMagentoLocaleLanguage(): string
    {
        return (string) $this->getData(self::FIELD_MAGENTO_LOCALE_LANGUAGE);
    }

    /**
     * @inheritdoc
     */
    public function getApiLocaleLanguage(): string
    {
        return (string) $this->getData(self::FIELD_API_LOCALE_LANGUAGE);
    }

    /**
     * @inheritdoc
     */
    public function setLanguageMappingId(int $value): LanguageMappingInterface
    {
        return $this->setId((int) $value);
    }

    /**
     * @inheritdoc
     */
    public function setMagentoLocaleLanguage(string $value): LanguageMappingInterface
    {
        return $this->setData(self::FIELD_MAGENTO_LOCALE_LANGUAGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setApiLocaleLanguage(string $value): LanguageMappingInterface
    {
        return $this->setData(self::FIELD_API_LOCALE_LANGUAGE, $value);
    }

    /**
     * Populate the object from array values
     * It is better to use setters instead of the generic setData method
     *
     * @param array $values
     *
     * @return LanguageMapping
     */
    public function populateFromArray(array $values)
    {
        if (array_key_exists(self::FIELD_MAGENTO_LOCALE_LANGUAGE, $values)) {
            $this->setMagentoLocaleLanguage($values[self::FIELD_MAGENTO_LOCALE_LANGUAGE]);
        }

        if (array_key_exists(self::FIELD_API_LOCALE_LANGUAGE, $values)) {
            $this->setApiLocaleLanguage($values[self::FIELD_API_LOCALE_LANGUAGE]);
        }

        return $this;
    }
}
