<?php
/**
 * Abstract Admin action for translatable content
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\TranslatableContent;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterfaceFactory as TranslatableContentFactory;
use TextMaster\TextMaster\Api\TranslatableContentRepositoryInterface   as TranslatableContentRepository;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterface        as TranslatableContent;
use TextMaster\TextMaster\Api\Data\TranslatableContentSearchResultsInterface;

abstract class AbstractAction extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var TranslatableContentFactory
     */
    protected $translatableContentFactory;

    /**
     * @var TranslatableContentRepository
     */
    protected $translatableContentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * AbstractAction constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param TranslatableContentFactory $translatableContentFactory
     * @param TranslatableContentRepository $translatableContentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context           $context,
        Registry          $coreRegistry,
        TranslatableContentFactory    $translatableContentFactory,
        TranslatableContentRepository $translatableContentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);

        $this->coreRegistry = $coreRegistry;
        $this->translatableContentFactory = $translatableContentFactory;
        $this->translatableContentRepository = $translatableContentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('TextMaster_TextMaster::configuration');
    }

    /**
     * Init the current TranslatableContent
     *
     * @param int|null $translatableContentId
     *
     * @return TranslatableContent
     * @throws NotFoundException
     */
    protected function initModel($translatableContentId): TranslatableContent
    {
        /** @var TranslatableContent $translatableContent */
        $translatableContent = $this->translatableContentFactory->create();

        // Initial checking
        if ($translatableContentId) {
            try {
                $translatableContent = $this->translatableContentRepository->getById($translatableContentId);
            } catch (NoSuchEntityException $e) {
                throw new NotFoundException(__('This translatable content does not exist'));
            }
        }
        return $translatableContent;
    }

    /**
     * @param $item
     * @param false $default
     * @return array
     */
    public function setDataFromAttributeViewCollection($item, $default = false)
    {
        $data[TranslatableContent::FIELD_DOCUMENT_TYPE] = $item->getDocumentType();
        $data[TranslatableContent::FIELD_ATTRIBUTE_CODE] = $item->getAttributeCode();
        $data[TranslatableContent::FIELD_SELECT_BY_DEFAULT] = $default;

        return $data;
    }

    /**
     * Check if an attribute already exists on textmaster_translatable_content table
     * with same document_type and attribute_code
     *
     * @param $data
     *
     * @return TranslatableContentSearchResultsInterface
     */
    public function getFilteredItem($data)
    {
        $this->searchCriteriaBuilder->addFilter(
            TranslatableContent::FIELD_DOCUMENT_TYPE,
            $data[TranslatableContent::FIELD_DOCUMENT_TYPE],
            'eq'
        );
        $this->searchCriteriaBuilder->addFilter(
            TranslatableContent::FIELD_ATTRIBUTE_CODE,
            $data[TranslatableContent::FIELD_ATTRIBUTE_CODE],
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->translatableContentRepository->getList($searchCriteria);
    }
}
