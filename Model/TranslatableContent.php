<?php
/**
 * Translatable Content Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use TextMaster\TextMaster\Model\ResourceModel\TranslatableContent as TranslatableContentResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class TranslatableContent extends AbstractModel implements TranslatableContentInterface, IdentityInterface
{
    /**
     * Translatable content cache tag
     */
    const CACHE_TAG = 'textmaster_translatable_content';

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
            TranslatableContentResourceModel::class
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
    public function getTranslatableContentId(): ?int
    {
        return (int) $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getDocumentType(): string
    {
        return (string) $this->getData(self::FIELD_DOCUMENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeCode(): string
    {
        return (string) $this->getData(self::FIELD_ATTRIBUTE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function getSelectByDefault(): bool
    {
        return (bool) $this->getData(self::FIELD_SELECT_BY_DEFAULT);
    }

    /**
     * @inheritdoc
     */
    public function setTranslatableContentId(int $value): TranslatableContentInterface
    {
        return $this->setId((int) $value);
    }

    /**
     * @inheritdoc
     */
    public function setDocumentType(string $value): TranslatableContentInterface
    {
        return $this->setData(self::FIELD_DOCUMENT_TYPE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setAttributeCode(string $value): TranslatableContentInterface
    {
        return $this->setData(self::FIELD_ATTRIBUTE_CODE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setSelectByDefault(bool $value): TranslatableContentInterface
    {
        return $this->setData(self::FIELD_SELECT_BY_DEFAULT, $value);
    }

    /**
     * Populate the object from array values
     * It is better to use setters instead of the generic setData method
     *
     * @param array $values
     *
     * @return TranslatableContent
     */
    public function populateFromArray(array $values)
    {
        if (array_key_exists(self::FIELD_DOCUMENT_TYPE, $values)) {
            $this->setDocumentType($values[self::FIELD_DOCUMENT_TYPE]);
        }

        if (array_key_exists(self::FIELD_ATTRIBUTE_CODE, $values)) {
            $this->setAttributeCode($values[self::FIELD_ATTRIBUTE_CODE]);
        }

        if (array_key_exists(self::FIELD_SELECT_BY_DEFAULT, $values)) {
            $this->setSelectByDefault((bool) $values[self::FIELD_SELECT_BY_DEFAULT]);
        }

        return $this;
    }
}
