<?php
/**
 * Admin Action : translatablecontent/disableToTranslate
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
 * Class DisableToTranslate
 * Delete attribute from table textmaster_translatable_content, so it's delete default value too
 */
class DisableToTranslate extends AbstractAction
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
     * DisableToTranslate constructor.
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
            $data = $this->setDataFromAttributeViewCollection($item, true);
            $translatableContentId = null;

            // 2. We filter with search criteria on translatableRepository to check
            // if same entry already exist in textmaster_translatable_content table
            $translatableContentRepository = $this->getFilteredItem($data);

            // 3. We get id of matching line in textmaster_translatable_content to load and delete the right item
            if ($translatableContentRepository->getTotalCount() === 1) {
                foreach ($translatableContentRepository->getItems() as $existingItem) {
                    $translatableContentId = $existingItem->getTranslatableContentId();
                }
            }
            $this->dataPersistor->set(TranslatableContent::CACHE_TAG, $data);

            if (!empty($translatableContentId)) {
                /** @var TranslatableContent $translatableContent */
                $translatableContent = $this->initModel($translatableContentId);
                $translatableContent->populateFromArray($data);
            }

            // 4. We delete loaded item
            try {
                // if id is null, it is because we have selected attributes not stored in table,
                // so we just won't have to delete them
                if (!empty($translatableContentId)) {
                    $this->translatableContentRepository->delete($translatableContent);
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
                    __('Something went wrong while deleting the attributes. %1', $e->getMessage())
                );
            }
        }
        if ($successMessage === true) {
            $this->messageManager->addSuccessMessage(__('All attributes selected have been disabled'));
        }

        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
}
