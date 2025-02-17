<?php
/**
 * Project Attribute Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\ProjectAttributeInterface;
use TextMaster\TextMaster\Model\ResourceModel\ProjectAttribute as ProjectAttributeResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ProjectAttribute extends AbstractModel implements ProjectAttributeInterface, IdentityInterface
{
    /**
     * Document cache tag
     */
    const CACHE_TAG = 'textmaster_document';

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
            ProjectAttributeResourceModel::class
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
    public function getProjectAttributeId(): ?int
    {
        return (int) $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function getProjectId(): int
    {
        return $this->getData(self::FIELD_PROJECT_ID);
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
    public function setProjectAttributeId(int $value): ProjectAttributeInterface
    {
        return $this->setId((int) $value);
    }

    /**
     * @inheritdoc
     */
    public function setProjectId(int $value): ProjectAttributeInterface
    {
        return $this->setData(self::FIELD_PROJECT_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setAttributeCode(string $value): ProjectAttributeInterface
    {
        return $this->setData(self::FIELD_ATTRIBUTE_CODE, $value);
    }
}
