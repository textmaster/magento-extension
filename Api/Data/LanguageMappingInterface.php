<?php
/**
 * Language Mapping Interface
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Api\Data;

interface LanguageMappingInterface
{
    /** const TABLE_NAME Name of the SQL TABLE */
    const TABLE_NAME = 'textmaster_language_mapping';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const FIELD_LANGUAGE_MAPPING_ID          = 'language_mapping_id';
    const FIELD_MAGENTO_LOCALE_LANGUAGE      = 'magento_locale_language';
    const FIELD_API_LOCALE_LANGUAGE          = 'api_locale_language';
    /**#@-*/

    /**
     * Get field: nomenclature_id
     *
     * @return int|null
     */
    public function getLanguageMappingId(): ?int;

    /**
     * Get field: magento_locale_language
     *
     * @return string
     */
    public function getMagentoLocaleLanguage(): string;

    /**
     * Get field: magento_locale_language
     *
     * @return string
     */
    public function getApiLocaleLanguage(): string;

    /**
     * Set field: language_mapping_id
     *
     * @param int $value
     *
     * @return LanguageMappingInterface
     */
    public function setLanguageMappingId(int $value): LanguageMappingInterface;

    /**
     * set field: magento_locale_language
     *
     * @param string $value
     *
     * @return LanguageMappingInterface
     */
    public function setMagentoLocaleLanguage(string $value): LanguageMappingInterface;

    /**
     * set field: api_locale_language
     *
     * @param string $value
     *
     * @return LanguageMappingInterface
     */
    public function setApiLocaleLanguage(string $value): LanguageMappingInterface;
}
