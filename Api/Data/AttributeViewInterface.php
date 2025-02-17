<?php
/**
 * Attribute View Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface AttributeViewInterface
{
    /** const TABLE_NAME Name of the SQL TABLE */
    const TABLE_NAME = 'textmaster_attribute_view';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_ATTRIBUTE_VIEW_ID       = 'attribute_view_id';
    const FIELD_DOCUMENT_TYPE           = 'document_type';
    const FIELD_ATTRIBUTE_CODE          = 'attribute_code';
    const FIELD_TRANSLATABLE            = 'translatable';
    const FIELD_SELECT_BY_DEFAULT       = 'select_by_default';
    /**#@-*/

    /**
     * Get field: attribute_view_id
     *
     * @return int|null
     */
    public function getAttributeViewId(): ?int;

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
     * Get field: translatable
     *
     * @return bool
     */
    public function getTranslatable(): bool;

    /**
     * Set field: attribute_view_id
     *
     * @param int $value
     *
     * @return AttributeViewInterface
     */
    public function setAttributeViewId(int $value): AttributeViewInterface;

    /**
     * set field: document_type
     *
     * @param string $value
     *
     * @return AttributeViewInterface
     */
    public function setDocumentType(string $value): AttributeViewInterface;

    /**
     * set field: attribute_code
     *
     * @param string $value
     *
     * @return AttributeViewInterface
     */
    public function setAttributeCode(string $value): AttributeViewInterface;

    /**
     * set field: default
     *
     * @param bool $value
     *
     * @return AttributeViewInterface
     */
    public function setSelectByDefault(bool $value): AttributeViewInterface;

    /**
     * set field: translatable
     *
     * @param bool $value
     *
     * @return AttributeViewInterface
     */
    public function setTranslatable(bool $value): AttributeViewInterface;
}
