<?php
/**
 * Translatable Content Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface TranslatableContentInterface
{
    /** const TABLE_NAME Name of the SQL TABLE */
    const TABLE_NAME = 'textmaster_translatable_content';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_TRANSLATABLE_CONTENT_ID = 'translatable_content_id';
    const FIELD_DOCUMENT_TYPE           = 'document_type';
    const FIELD_ATTRIBUTE_CODE          = 'attribute_code';
    const FIELD_SELECT_BY_DEFAULT       = 'select_by_default';
    /**#@-*/

    /**
     * Document type availables
     */
    const DOCUMENT_TYPE_BLOCK = 'block';
    const DOCUMENT_TYPE_CATEGORY = 'category';
    const DOCUMENT_TYPE_PAGE = 'page';
    const DOCUMENT_TYPE_PRODUCT = 'product';
    const DOCUMENT_TYPE_LIST = [
        self::DOCUMENT_TYPE_BLOCK,
        self::DOCUMENT_TYPE_CATEGORY,
        self::DOCUMENT_TYPE_PAGE,
        self::DOCUMENT_TYPE_PRODUCT
    ];

    /**
     * Get field: translatable_content_id
     *
     * @return int|null
     */
    public function getTranslatableContentId(): ?int;

    /**
     * Get field: document_type
     *
     * @return string
     */
    public function getDocumentType(): string;

    /**
     * Get field: attribute_code
     *
     * @return string
     */
    public function getAttributeCode(): string;

    /**
     * Get field: select_by_default
     *
     * @return bool
     */
    public function getSelectByDefault(): bool;

    /**
     * Set field: translatable_content_id
     *
     * @param int $value
     *
     * @return TranslatableContentInterface
     */
    public function setTranslatableContentId(int $value): TranslatableContentInterface;

    /**
     * set field: document_type
     *
     * @param string $value
     *
     * @return TranslatableContentInterface
     */
    public function setDocumentType(string $value): TranslatableContentInterface;

    /**
     * set field: attribute_code
     *
     * @param string $value
     *
     * @return TranslatableContentInterface
     */
    public function setAttributeCode(string $value): TranslatableContentInterface;

    /**
     * set field: select_by_default
     *
     * @param bool $value
     *
     * @return TranslatableContentInterface
     */
    public function setSelectByDefault(bool $value): TranslatableContentInterface;
}
