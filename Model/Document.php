<?php
/**
 * Document Model
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model;

use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface;
use TextMaster\TextMaster\Model\ResourceModel\Document as DocumentResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Document extends AbstractModel implements DocumentInterface, IdentityInterface
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
     * Project entity
     *
     * @var ProjectInterface
     */
    protected $project;

    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProjectRepositoryInterface $projectRepository
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProjectRepositoryInterface $projectRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->projectRepository = $projectRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Magento Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            DocumentResourceModel::class
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
    public function getDocumentId(): ?int
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
    public function getProjectId(): int
    {
        return (int) $this->getData(self::FIELD_PROJECT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getMagentoEntityId(): int
    {
        return (int) $this->getData(self::FIELD_MAGENTO_ENTITY_ID);
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
    public function getStatus(): string
    {
        return (string) $this->getData(self::FIELD_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): string
    {
        return (string) $this->getData(self::FIELD_ERROR_MESSAGE);
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
    public function setDocumentId(int $value): DocumentInterface
    {
        return $this->setId((int) $value);
    }

    /**
     * @inheritdoc
     */
    public function setTextMasterId(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_TEXTMASTER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setToken(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_TOKEN, $value);
    }

    /**
     * @inheritdoc
     */
    public function setProjectId(int $value): DocumentInterface
    {
        return $this->setData(self::FIELD_PROJECT_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setMagentoEntityId(int $value): DocumentInterface
    {
        return $this->setData(self::FIELD_MAGENTO_ENTITY_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_STATUS, $value);
    }

    /**
     * @inheritdoc
     */
    public function setErrorMessage(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_ERROR_MESSAGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_CREATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_UPDATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setStartTranslationAt(?string $value): DocumentInterface
    {
        return $this->setData(self::FIELD_START_TRANSLATION_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setIsApplied(bool $value): DocumentInterface
    {
        return $this->setData(self::FIELD_IS_APPLIED, $value);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getProject()
    {
        if (!$this->getProjectId()) {
            return false;
        }
        if (empty($this->project)) {
            $this->project = $this->projectRepository->getById($this->getProjectId());
        }
        return $this->project;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project): DocumentInterface
    {
        $this->project = $project;
        $this->setProjectId($project->getProjectId());
        return $this;
    }
}
