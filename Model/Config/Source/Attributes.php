<?php
/**
 * Attributes Option Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use TextMaster\TextMaster\Api\TranslatableContentRepositoryInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class Attributes implements OptionSourceInterface
{
    /**
     * @var TranslatableContentRepositoryInterface
     */
    protected $translatableContentRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var string
     */
    protected $documentType;

    /**
     * Constructor
     *
     * @param TranslatableContentRepositoryInterface $translatableContentRepository
     * @param AttributeRepositoryInterface           $attributeRepository
     * @param SearchCriteriaBuilderFactory           $searchCriteriaBuilderFactory
     * @param SortOrderBuilder                       $sortOrderBuilder
     * @param string                                 $documentType
     */
    public function __construct(
        TranslatableContentRepositoryInterface $translatableContentRepository,
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilder $sortOrderBuilder,
        string $documentType = ''
    ) {
        $this->translatableContentRepository = $translatableContentRepository;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->documentType = $documentType;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $options = [];
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        /** @var SortOrder $sortOrder */
        $sortOrders[] = $this->sortOrderBuilder->setField(TranslatableContentInterface::FIELD_SELECT_BY_DEFAULT)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        $sortOrders[] = $this->sortOrderBuilder->setField(TranslatableContentInterface::FIELD_ATTRIBUTE_CODE)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();

        $searchCriteriaBuilder->setSortOrders($sortOrders);
        if ($this->documentType) {
            $searchCriteriaBuilder->addFilter(TranslatableContentInterface::FIELD_DOCUMENT_TYPE, $this->documentType);
        }
        $searchCriteria = $searchCriteriaBuilder->create();
        $translatableContents = $this->translatableContentRepository->getList($searchCriteria);
        foreach ($translatableContents->getItems() as $translatableContent) {
            $label = '';
            if (in_array($translatableContent->getDocumentType(), ['category', 'product'])) {
                $entityTypeCode = CategoryAttributeInterface::ENTITY_TYPE_CODE;
                if ($translatableContent->getDocumentType() === 'product') {
                    $entityTypeCode = ProductAttributeInterface::ENTITY_TYPE_CODE;
                }
                $attribute = $this->attributeRepository->get($entityTypeCode, $translatableContent->getAttributeCode());
                $label = $attribute->getDefaultFrontendLabel();
            }
            if (in_array($translatableContent->getDocumentType(), ['page', 'block'])) {
                $label = __(
                    'attribute_' .
                    $translatableContent->getDocumentType() .
                    '_' .
                    $translatableContent->getAttributeCode()
                );
            }
            $options[$translatableContent->getAttributeCode()] =
                $label . ' (' . $translatableContent->getAttributeCode() . ')';
        }
        return $options;
    }
}
