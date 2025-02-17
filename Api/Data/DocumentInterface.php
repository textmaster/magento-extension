<?php
/**
 * Document Data Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface DocumentInterface
{
    /** const TABLE_NAME Name of the SQL TABLE */
    const TABLE_NAME = 'textmaster_document';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_DOCUMENT_ID             = 'document_id';
    const FIELD_TEXTMASTER_ID           = 'textmaster_id';
    const FIELD_TOKEN                   = 'token';
    const FIELD_PROJECT_ID              = 'project_id';
    const FIELD_MAGENTO_ENTITY_ID       = 'magento_entity_id';
    const FIELD_NAME                    = 'name';
    const FIELD_STATUS                  = 'status';
    const FIELD_CREATED_AT              = 'created_at';
    const FIELD_UPDATED_AT              = 'updated_at';
    const FIELD_START_TRANSLATION_AT    = 'start_translation_at';
    const FIELD_IS_APPLIED              = 'is_applied';
    const FIELD_ERROR_MESSAGE           = 'error_message';
    /**#@-*/

    /**
     * Document status available in API
     */
    const STATUS_WAITING_ASSIGNMENT = 'waiting_assignment';
    const STATUS_IN_CREATION = 'in_creation';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COPYSCAPE = 'copyscape';
    const STATUS_COUNTING_WORDS = 'counting_words';
    const STATUS_QUALITY_CONTROL = 'quality_control';

    const STATUS_LIST = [
        self::STATUS_WAITING_ASSIGNMENT,
        self::STATUS_IN_CREATION,
        self::STATUS_IN_PROGRESS,
        self::STATUS_IN_REVIEW,
        self::STATUS_COMPLETED,
        self::STATUS_INCOMPLETE,
        self::STATUS_PAUSED,
        self::STATUS_CANCELED,
        self::STATUS_COPYSCAPE,
        self::STATUS_COUNTING_WORDS,
        self::STATUS_QUALITY_CONTROL
    ];

    /**
     * Get field: document_id
     *
     * @return int|null
     */
    public function getDocumentId(): ?int;

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
     * Get field: project_id
     *
     * @return int
     */
    public function getProjectId(): int;

    /**
     * Get Project
     *
     * @return ProjectInterface|false
     */
    public function getProject();

    /**
     * Get field: magento_entity_id
     *
     * @return int
     */
    public function getMagentoEntityId(): int;

    /**
     * Get field: name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get field: status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get field: error_message
     *
     * @return string
     */
    public function getErrorMessage(): string;

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
     * Set field: document_id
     *
     * @param int $value
     *
     * @return DocumentInterface
     */
    public function setDocumentId(int $value): DocumentInterface;

    /**
     * set field: textmaster_id
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setTextMasterId(string $value): DocumentInterface;

    /**
     * set field: token
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setToken(string $value): DocumentInterface;

    /**
     * Set field: project_id
     *
     * @param int $value
     *
     * @return DocumentInterface
     */
    public function setProjectId(int $value): DocumentInterface;

    /**
     * Set Project
     *
     * @param ProjectInterface $project
     *
     * @return DocumentInterface
     */
    public function setProject(ProjectInterface $project): DocumentInterface;

    /**
     * Set field: magento_entity_id
     *
     * @param int $value
     *
     * @return DocumentInterface
     */
    public function setMagentoEntityId(int $value): DocumentInterface;

    /**
     * Set field: name
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setName(string $value): DocumentInterface;

    /**
     * Set field: status
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setStatus(string $value): DocumentInterface;

    /**
     * Set field: error_message
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setErrorMessage(string $value): DocumentInterface;

    /**
     * Set field: created_at
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setCreatedAt(string $value): DocumentInterface;

    /**
     * Set field: updated_at
     *
     * @param string $value
     *
     * @return DocumentInterface
     */
    public function setUpdatedAt(string $value): DocumentInterface;

    /**
     * Set field: start_translation_at
     *
     * @param string|null $value
     *
     * @return DocumentInterface
     */
    public function setStartTranslationAt(?string $value): DocumentInterface;

    /**
     * set field: is_applied
     *
     * @param bool $value
     *
     * @return DocumentInterface
     */
    public function setIsApplied(bool $value): DocumentInterface;
}
