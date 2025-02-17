<?php
/**
 * Project Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Model\ResourceModel\Project as ProjectResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Project extends AbstractModel implements ProjectInterface, IdentityInterface
{
    /**
     * Project cache tag
     */
    const CACHE_TAG = 'textmaster_project';

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
            ProjectResourceModel::class
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
    public function getProjectId(): ?int
    {
        return (int) $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getTextMasterId(): string
    {
        return (string) $this->getData(self::FIELD_TEXTMASTER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getToken(): string
    {
        return (string) $this->getData(self::FIELD_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return (string) $this->getData(self::FIELD_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getDueDate(): ?string
    {
        return $this->getData(self::FIELD_DUE_DATE);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId(): ?string
    {
        return $this->getData(self::FIELD_TEMPLATE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getSourceLanguage(): string
    {
        return (string) $this->getData(self::FIELD_SOURCE_LANGUAGE);
    }

    /**
     * @inheritdoc
     */
    public function getSourceStoreId(): ?int
    {
        return (int)$this->getData(self::FIELD_SOURCE_STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getTargetLanguage(): string
    {
        return (string) $this->getData(self::FIELD_TARGET_LANGUAGE);
    }

    /**
     * @inheritdoc
     */
    public function getTargetStoreId(): ?int
    {
        return (int)$this->getData(self::FIELD_TARGET_STORE_ID);
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
    public function getPrice(): ?float
    {
        return (float)$this->getData(self::FIELD_PRICE);
    }

    /**
     * @inheritdoc
     */
    public function getCurrency(): string
    {
        return (string) $this->getData(self::FIELD_CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function getNumberOfDocuments(): int
    {
        return (int) $this->getData(self::FIELD_NUMBER_OF_DOCUMENTS);
    }

    /**
     * @inheritdoc
     */
    public function getTotalWordCount(): ?int
    {
        return (int)$this->getData(self::FIELD_TOTAL_WORD_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function getProgress(): float
    {
        return (float) $this->getData(self::FIELD_PROGRESS);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return (string) $this->getData(self::FIELD_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteValidated(): bool
    {
        return (bool) $this->getData(self::FIELD_QUOTE_VALIDATED);
    }

    /**
     * @inheritdoc
     */
    public function getAutolaunch(): bool
    {
        return (bool) $this->getData(self::FIELD_AUTOLAUNCH);
    }

    /**
     * @inheritdoc
     */
    public function getNotes(): string
    {
        return (string) $this->getData(self::FIELD_NOTES);
    }

    /**
     * @inheritdoc
     */
    public function getProjectType(): string
    {
        return (string) $this->getData(self::FIELD_PROJECT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getLanguageLevel(): string
    {
        return (string) $this->getData(self::FIELD_LANGUAGE_LEVEL);
    }

    /**
     * @inheritdoc
     */
    public function getCategory(): string
    {
        return (string) $this->getData(self::FIELD_CATEGORY);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?string
    {
        return (string) $this->getData(self::FIELD_CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?string
    {
        return (string) $this->getData(self::FIELD_UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function getStartTranslationAt(): ?string
    {
        return (string) $this->getData(self::FIELD_START_TRANSLATION_AT);
    }

    /**
     * @inheritdoc
     */
    public function getIsApplied(): bool
    {
        return (bool) $this->getData(self::FIELD_IS_APPLIED);
    }

    /**
     * @inheritdoc
     */
    public function setProjectId(int $value): ProjectInterface
    {
        return $this->setId((int) $value);
    }

    /**
     * @inheritdoc
     */
    public function setTextMasterId(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_TEXTMASTER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function setDueDate(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_DUE_DATE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setTemplateId(?string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_TEMPLATE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setToken(?string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_TOKEN, $value);
    }

    /**
     * @inheritdoc
     */
    public function setSourceLanguage(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_SOURCE_LANGUAGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setSourceStoreId(int $value): ProjectInterface
    {
        return $this->setData(self::FIELD_SOURCE_STORE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setTargetLanguage(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_TARGET_LANGUAGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setTargetStoreId(int $value): ProjectInterface
    {
        return $this->setData(self::FIELD_TARGET_STORE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setDocumentType(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_DOCUMENT_TYPE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setPrice(?float $value): ProjectInterface
    {
        return $this->setData(self::FIELD_PRICE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setCurrency(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_CURRENCY, $value);
    }

    /**
     * @inheritdoc
     */
    public function setNumberOfDocuments(int $value): ProjectInterface
    {
        return $this->setData(self::FIELD_NUMBER_OF_DOCUMENTS, $value);
    }

    /**
     * @inheritdoc
     */
    public function setTotalWordCount(?int $value): ProjectInterface
    {
        return $this->setData(self::FIELD_TOTAL_WORD_COUNT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setProgress(float $value): ProjectInterface
    {
        return $this->setData(self::FIELD_PROGRESS, $value);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_STATUS, $value);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteValidated(bool $value): ProjectInterface
    {
        return $this->setData(self::FIELD_QUOTE_VALIDATED, $value);
    }

    /**
     * @inheritdoc
     */
    public function setAutolaunch(bool $value): ProjectInterface
    {
        return $this->setData(self::FIELD_AUTOLAUNCH, $value);
    }

    /**
     * @inheritdoc
     */
    public function setNotes(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_NOTES, $value);
    }

    /**
     * @inheritdoc
     */
    public function setProjectType(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_PROJECT_TYPE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setLanguageLevel(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_LANGUAGE_LEVEL, $value);
    }

    /**
     * @inheritdoc
     */
    public function setCategory(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_CATEGORY, $value);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_CREATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_UPDATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setStartTranslationAt(?string $value): ProjectInterface
    {
        return $this->setData(self::FIELD_START_TRANSLATION_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setIsApplied(bool $value): ProjectInterface
    {
        return $this->setData(self::FIELD_IS_APPLIED, $value);
    }

    /**
     * Populate the object from array values
     * It is better to use setters instead of the generic setData method
     *
     * @param array $values
     *
     * @return Project
     */
    public function populateFromArray(array $values): Project
    {
        if (array_key_exists(self::FIELD_TEXTMASTER_ID, $values)) {
            $this->setTextMasterId($values[self::FIELD_TEXTMASTER_ID]);
        }

        if (array_key_exists(self::FIELD_NAME, $values)) {
            $this->setName($values[self::FIELD_NAME]);
        }

        if (array_key_exists(self::FIELD_DUE_DATE, $values)) {
            $this->setDueDate($values[self::FIELD_DUE_DATE]);
        }

        if (array_key_exists(self::FIELD_TEMPLATE_ID, $values)) {
            $this->setTemplateId($values[self::FIELD_TEMPLATE_ID]);
        }

        if (array_key_exists(self::FIELD_SOURCE_LANGUAGE, $values)) {
            $this->setSourceLanguage($values[self::FIELD_SOURCE_LANGUAGE]);
        }

        if (array_key_exists(self::FIELD_SOURCE_STORE_ID, $values)) {
            $this->setSourceStoreId((int) $values[self::FIELD_SOURCE_STORE_ID]);
        }

        if (array_key_exists(self::FIELD_TARGET_LANGUAGE, $values)) {
            $this->setTargetLanguage($values[self::FIELD_TARGET_LANGUAGE]);
        }

        if (array_key_exists(self::FIELD_TARGET_STORE_ID, $values)) {
            $this->setTargetStoreId((int) $values[self::FIELD_TARGET_STORE_ID]);
        }

        if (array_key_exists(self::FIELD_DOCUMENT_TYPE, $values)) {
            $this->setDocumentType($values[self::FIELD_DOCUMENT_TYPE]);
        }

        if (array_key_exists(self::FIELD_NUMBER_OF_DOCUMENTS, $values)) {
            $this->setNumberOfDocuments((int) $values[self::FIELD_NUMBER_OF_DOCUMENTS]);
        }

        if (array_key_exists(self::FIELD_STATUS, $values)) {
            $this->setStatus($values[self::FIELD_STATUS]);
        }

        if (array_key_exists(self::FIELD_NOTES, $values)) {
            $this->setNotes($values[self::FIELD_NOTES]);
        }

        if (array_key_exists(self::FIELD_PROJECT_TYPE, $values)) {
            $this->setProjectType($values[self::FIELD_PROJECT_TYPE]);
        }

        if (array_key_exists(self::FIELD_LANGUAGE_LEVEL, $values)) {
            $this->setLanguageLevel($values[self::FIELD_LANGUAGE_LEVEL]);
        }

        if (array_key_exists(self::FIELD_CATEGORY, $values)) {
            $this->setCategory($values[self::FIELD_CATEGORY]);
        }

        return $this;
    }
}
