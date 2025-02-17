<?php
/**
 * Project Attribute Data Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface ProjectAttributeInterface
{
    /** const TABLE_NAME Name of the SQL TABLE */
    const TABLE_NAME = 'textmaster_project_attribute';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_PROJECT_ATTRIBUTE_ID = 'project_attribute_id';
    const FIELD_PROJECT_ID  = 'project_id';
    const FIELD_ATTRIBUTE_CODE   = 'attribute_code';
    /**#@-*/

    /**
     * Get field: project_attribute_id
     *
     * @return int|null
     */
    public function getProjectAttributeId(): ?int;

    /**
     * Get field: project_id
     *
     * @return int
     */
    public function getProjectId(): int;

    /**
     * Get field: attribute_code
     *
     * @return string
     */
    public function getAttributeCode(): string;

    /**
     * Set field: project_attribute_id
     *
     * @param int $value
     *
     * @return ProjectAttributeInterface
     */
    public function setProjectAttributeId(int $value): ProjectAttributeInterface;

    /**
     * Set field: project_id
     *
     * @param int $value
     *
     * @return ProjectAttributeInterface
     */
    public function setProjectId(int $value): ProjectAttributeInterface;

    /**
     * Set field: attribute_code
     *
     * @param string $value
     *
     * @return ProjectAttributeInterface
     */
    public function setAttributeCode(string $value): ProjectAttributeInterface;
}
