<?php
/**
 * Create TextMaster Attribute View table for listing all attributes together
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateTextmasterAttributeView implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    protected $schemaSetup;

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $viewToTextmasterAttributeView = 'textmaster_attribute_view';
        $eavAttributeTable = $this->schemaSetup->getTable('eav_attribute');
        $eavEntityTypeTable = $this->schemaSetup->getTable('eav_entity_type');
        $textmasterTranslatableContentTable = $this->schemaSetup->getTable('textmaster_translatable_content');

        $sql = "CREATE
                SQL SECURITY INVOKER
                VIEW {$viewToTextmasterAttributeView}
                  AS
                    SELECT
                        ROW_NUMBER() OVER (
                            ORDER BY document_type, attribute_code
                        ) attribute_view_id,
                        document_type_attribute.document_type AS document_type,
                        document_type_attribute.attribute_code AS attribute_code,
                        document_type_attribute.attribute_label AS attribute_label,
                        ttc.translatable_content_id IS NOT NULL AS translatable,
                        IFNULL(ttc.select_by_default, 0) AS select_by_default
                    FROM
                        (
                            (
                                SELECT
                                    DISTINCT ea.attribute_code AS attribute_code,
                                    'product' AS document_type,
                                    ea.frontend_label AS attribute_label
                                FROM
                                    {$eavAttributeTable} ea
                                    JOIN {$eavEntityTypeTable} eet
                                    ON ea.entity_type_id = eet.entity_type_id
                                WHERE
                                    eet.entity_type_code = 'catalog_product'
                                    AND ea.backend_type IN ('varchar', 'text')
                                UNION
                                SELECT
                                    DISTINCT ea.attribute_code AS attribute_code,
                                    'category' AS category,
                                    ea.frontend_label AS attribute_label
                                FROM
                                    {$eavAttributeTable} ea
                                    JOIN {$eavEntityTypeTable} eet
                                    ON ea.entity_type_id = eet.entity_type_id
                                WHERE
                                    eet.entity_type_code = 'catalog_category'
                                    AND ea.backend_type in ('varchar', 'text')
                                UNION
                                SELECT
                                    'title' AS attribute_code,
                                    'page' AS document_type,
                                    'attribute_page_title' AS attribute_label
                                UNION
                                SELECT
                                    'meta_title' AS attribute_code,
                                    'page' AS document_type,
                                    'attribute_page_meta_title' AS attribute_label
                                UNION
                                SELECT
                                    'meta_keywords' AS attribute_code,
                                    'page' AS document_type,
                                    'attribute_page_meta_keywords' AS attribute_label
                                UNION
                                SELECT
                                    'meta_description' AS attribute_code,
                                    'page' AS document_type,
                                    'attribute_page_meta_description' AS attribute_label
                                UNION
                                SELECT
                                    'content_heading' AS attribute_code,
                                    'page' AS document_type,
                                    'attribute_page_content_heading' AS attribute_label
                                UNION
                                SELECT
                                    'content' AS attribute_code,
                                    'page' AS document_type,
                                    'attribute_page_content' AS attribute_label
                                UNION
                                SELECT
                                    'title' AS attribute_code,
                                    'block' AS document_type,
                                    'attribute_block_title' AS attribute_label
                                UNION
                                SELECT
                                    'content' AS attribute_code,
                                    'block' AS document_type,
                                    'attribute_block_content' AS attribute_label
                            ) document_type_attribute
                            LEFT JOIN {$textmasterTranslatableContentTable} ttc
                            ON document_type_attribute.document_type = ttc.document_type
                            AND document_type_attribute.attribute_code = ttc.attribute_code
                        )";
        $this->schemaSetup->getConnection()->query($sql);
        $this->schemaSetup->endSetup();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
