<?php
/**
 * Project Data Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface ProjectInterface
{
    /** const TABLE_NAME Name of the SQL TABLE */
    const TABLE_NAME = 'textmaster_project';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_PROJECT_ID              = 'project_id';
    const FIELD_TEXTMASTER_ID           = 'textmaster_id';
    const FIELD_TOKEN                   = 'token';
    const FIELD_NAME                    = 'name';
    const FIELD_DUE_DATE                = 'due_date';
    const FIELD_TEMPLATE_ID             = 'template_id';
    const FIELD_SOURCE_LANGUAGE         = 'source_language';
    const FIELD_SOURCE_STORE_ID         = 'source_store_id';
    const FIELD_TARGET_LANGUAGE         = 'target_language';
    const FIELD_TARGET_STORE_ID         = 'target_store_id';
    const FIELD_DOCUMENT_TYPE           = 'document_type';
    const FIELD_PRICE                   = 'price';
    const FIELD_CURRENCY                = 'currency';
    const FIELD_NUMBER_OF_DOCUMENTS     = 'number_of_documents';
    const FIELD_TOTAL_WORD_COUNT        = 'total_word_count';
    const FIELD_PROGRESS                = 'progress';
    const FIELD_STATUS                  = 'status';
    const FIELD_QUOTE_VALIDATED         = 'quote_validated';
    const FIELD_AUTOLAUNCH              = 'autolaunch';
    const FIELD_NOTES                   = 'notes';
    const FIELD_PROJECT_TYPE            = 'project_type';
    const FIELD_LANGUAGE_LEVEL          = 'language_level';
    const FIELD_CATEGORY                = 'category';
    const FIELD_CREATED_AT              = 'created_at';
    const FIELD_UPDATED_AT              = 'updated_at';
    const FIELD_START_TRANSLATION_AT    = 'start_translation_at';
    const FIELD_IS_APPLIED              = 'is_applied';
    /**#@-*/

    /**
     * Project status available in API
     */
    const STATUS_IN_CREATION = 'in_creation';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELED = 'canceled';

    const STATUS_LIST = [
        self::STATUS_IN_CREATION,
        self::STATUS_IN_PROGRESS,
        self::STATUS_IN_REVIEW,
        self::STATUS_COMPLETED,
        self::STATUS_PAUSED,
        self::STATUS_CANCELED
    ];

    /**
     * Get field: project_id
     *
     * @return int|null
     */
    public function getProjectId(): ?int;

    /**
     * Get field: textmaster_id
     *
     * @return string
     */
    public function getTextMasterId(): string;

    /**
     * Get field: token
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Get field: name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get field: due_date
     *
     * @return string|null
     */
    public function getDueDate(): ?string;

    /**
     * Get field: template_id
     *
     * @return string|null
     */
    public function getTemplateId(): ?string;

    /**
     * Get field: source_language
     *
     * @return string
     */
    public function getSourceLanguage(): string;

    /**
     * Get field: source_store_id
     *
     * @return int|null
     */
    public function getSourceStoreId(): ?int;

    /**
     * Get field: target_language
     *
     * @return string
     */
    public function getTargetLanguage(): string;

    /**
     * Get field: target_store_id
     *
     * @return int|null
     */
    public function getTargetStoreId(): ?int;

    /**
     * Get field: document_type
     *
     * @return string
     */
    public function getDocumentType(): string;

    /**
     * Get field: price
     *
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * Get field: currency
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Get number_of_documents
     *
     * @return int
     */
    public function getNumberOfDocuments(): int;

    /**
     * Get field: total_word_count
     *
     * @return int|null
     */
    public function getTotalWordCount(): ?int;

    /**
     * Get field: progress
     *
     * @return float
     */
    public function getProgress(): float;

    /**
     * Get field: status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get field: quote_validated
     *
     * @return bool
     */
    public function getQuoteValidated(): bool;

    /**
     * Get field: autolaunch
     *
     * @return bool
     */
    public function getAutolaunch(): bool;

    /**
     * Get field: notes
     *
     * @return string
     */
    public function getNotes(): string;

    /**
     * Get field: project_type
     *
     * @return string
     */
    public function getProjectType(): string;

    /**
     * Get field: language_level
     *
     * @return string
     */
    public function getLanguageLevel(): string;

    /**
     * Get field: category
     *
     * @return string
     */
    public function getCategory(): string;

    /**
     * Get field: created_at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Get field: updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Get field: start_translation_at
     *
     * @return string|null
     */
    public function getStartTranslationAt(): ?string;

    /**
     * Get field: is_applied
     *
     * @return bool
     */
    public function getIsApplied(): bool;

    /**
     * Set field: project_id
     *
     * @param int $value
     *
     * @return ProjectInterface
     */
    public function setProjectId(int $value): ProjectInterface;

    /**
     * set field: textmaster_id
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setTextMasterId(string $value): ProjectInterface;

    /**
     * set field: token
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setToken(string $value): ProjectInterface;

    /**
     * Set field: name
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setName(string $value): ProjectInterface;

    /**
     * Set field: due_date
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setDueDate(string $value): ProjectInterface;

    /**
     * Set field: template_id
     *
     * @param string|null $value
     *
     * @return ProjectInterface
     */
    public function setTemplateId(?string $value): ProjectInterface;

    /**
     * Set field: source_language
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setSourceLanguage(string $value): ProjectInterface;

    /**
     * Set field: source_store_id
     *
     * @param int $value
     *
     * @return ProjectInterface
     */
    public function setSourceStoreId(int $value): ProjectInterface;

    /**
     * Set field: target_language
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setTargetLanguage(string $value): ProjectInterface;

    /**
     * Set field: target_store_id
     *
     * @param int $value
     *
     * @return ProjectInterface
     */
    public function setTargetStoreId(int $value): ProjectInterface;

    /**
     * Set field: document_type
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setDocumentType(string $value): ProjectInterface;

    /**
     * Set field: price
     *
     * @param float|null $value
     *
     * @return ProjectInterface
     */
    public function setPrice(?float $value): ProjectInterface;

    /**
     * Set field: currency
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setCurrency(string $value): ProjectInterface;

    /**
     * Set field: number_of_documents
     *
     * @param int $value
     *
     * @return ProjectInterface
     */
    public function setNumberOfDocuments(int $value): ProjectInterface;

    /**
     * Set field: total_word_count
     *
     * @param int|null $value
     *
     * @return ProjectInterface
     */
    public function setTotalWordCount(?int $value): ProjectInterface;

    /**
     * Set field: progress
     *
     * @param float $value
     *
     * @return ProjectInterface
     */
    public function setProgress(float $value): ProjectInterface;

    /**
     * Set field: status
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setStatus(string $value): ProjectInterface;

    /**
     * set field: quote_validated
     *
     * @param bool $value
     *
     * @return ProjectInterface
     */
    public function setQuoteValidated(bool $value): ProjectInterface;

    /**
     * set field: autolaunch
     *
     * @param bool $value
     *
     * @return ProjectInterface
     */
    public function setAutolaunch(bool $value): ProjectInterface;

    /**
     * Set field: notes
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setNotes(string $value): ProjectInterface;

    /**
     * Set field: project_type
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setProjectType(string $value): ProjectInterface;

    /**
     * Set field: language_level
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setLanguageLevel(string $value): ProjectInterface;

    /**
     * Set field: category
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setCategory(string $value): ProjectInterface;

    /**
     * Set field: created_at
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setCreatedAt(string $value): ProjectInterface;

    /**
     * Set field: updated_at
     *
     * @param string $value
     *
     * @return ProjectInterface
     */
    public function setUpdatedAt(string $value): ProjectInterface;

    /**
     * Set field: start_translation_at
     *
     * @param string|null $value
     *
     * @return ProjectInterface
     */
    public function setStartTranslationAt(?string $value): ProjectInterface;

    /**
     * set field: is_applied
     *
     * @param bool $value
     *
     * @return ProjectInterface
     */
    public function setIsApplied(bool $value): ProjectInterface;
}
