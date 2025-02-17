<?php
/**
 * Admin Action : translatablecontent/enableToTranslate
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\TranslatableContent;

use TextMaster\TextMaster\Api\Data\TranslatableContentInterfaceFactory as TranslatableContentFactory;
use TextMaster\TextMaster\Api\TranslatableContentRepositoryInterface as TranslatableContentRepository;
use TextMaster\TextMaster\Model\TranslatableContent;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;

use TextMaster\TextMaster\Model\ResourceModel\AttributeView\Grid\CollectionFactory as AttributeViewCollectionFactory;

/**
 * Class EnableToTranslate
 * Add new attribute in textmaster_translatable_content if it doesn't exist
 */
class EnableToTranslate extends AbstractAction
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var AttributeViewCollectionFactory
     */
    protected $attributeViewCollectionFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * EnableToTranslate constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param TranslatableContentFactory $translatableContentFactory
     * @param TranslatableContentRepository $translatableContentRepository
     * @param Filter $filter
     * @param AttributeViewCollectionFactory $attributeViewCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TranslatableContentFactory $translatableContentFactory,
        TranslatableContentRepository $translatableContentRepository,
        Filter $filter,
        AttributeViewCollectionFactory $attributeViewCollectionFactory,
        DataPersistorInterface $dataPersistor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $translatableContentFactory,
            $translatableContentRepository,
            $searchCriteriaBuilder
        );
        $this->filter = $filter;
        $this->attributeViewCollectionFactory = $attributeViewCollectionFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return ResultRedirect
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

        // 1. We load datas from textmaster_attribute_view view with attributeView collection.
        // This will only load datas from ids selected.
        $attributeViewCollection = $this->filter->getCollection($this->attributeViewCollectionFactory->create());
        $successMessage = false;

        foreach ($attributeViewCollection as $item) {
            $data = $this->setDataFromAttributeViewCollection($item, $item->getSelectByDefault());

            $this->dataPersistor->set(TranslatableContent::CACHE_TAG, $data);

            $translatableContentId = null;

            /** @var TranslatableContent $translatableContent */
            $translatableContent = $this->initModel($translatableContentId);
            $translatableContent->populateFromArray($data);

            // 2. We filter with search criteria on translatableRepository to check
            // if same entry already exist in textmaster_translatable_content table
            $translatableContentRepository = $this->getFilteredItem($data);

            // 3. We save a new entry if line does not already exist, and we print a success message
            // if nothing went wrong or if data is already correctly saved
            try {
                if ($translatableContentRepository->getTotalCount() < 1) {
                    $this->translatableContentRepository->save($translatableContent);
                }
                // set successmessage flag to true
                $successMessage = true;

                $this->dataPersistor->clear(TranslatableContent::CACHE_TAG);
            } catch (LocalizedException $e) {
                $successMessage = false;
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $successMessage = false;
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the attributes. %1', $e->getMessage())
                );
            }
        }
        if ($successMessage === true) {
            $this->messageManager->addSuccessMessage(__('All attributes selected have been enabled'));
        }

        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
}
