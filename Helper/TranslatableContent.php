<?php
/**
 * TextMaster Translatable Content helper
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Helper;

use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use TextMaster\TextMaster\Api\TranslatableContentRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

class TranslatableContent
{
    /**
     * @var TranslatableContentRepositoryInterface
     */
    protected $translatableContentRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * Constructor
     *
     * @param TranslatableContentRepositoryInterface $translatableContentRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        TranslatableContentRepositoryInterface $translatableContentRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->translatableContentRepository = $translatableContentRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @return array
     */
    public function getSelectByDefaultAttributes(): array
    {
        $selectByDefaultAttributes = [];
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(TranslatableContentInterface::FIELD_SELECT_BY_DEFAULT, 1);
        $searchCriteria = $searchCriteriaBuilder->create();

        $translatableContents = $this->translatableContentRepository->getList($searchCriteria);
        foreach ($translatableContents->getItems() as $translatableContent) {
            $selectByDefaultAttributes[
                $translatableContent->getDocumentType() . '_attributes'
            ][] = $translatableContent->getAttributeCode();
        }
        return $selectByDefaultAttributes;
    }
}
