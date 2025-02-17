<?php
/**
 * Admin Action : languagemapping/save
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\LanguageMapping;

use TextMaster\TextMaster\Api\Data\LanguageMappingSearchResultsInterface;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use TextMaster\TextMaster\Api\Data\LanguageMappingInterfaceFactory as LanguageMappingFactory;
use TextMaster\TextMaster\Api\LanguageMappingRepositoryInterface   as LanguageMappingRepository;
use TextMaster\TextMaster\Model\LanguageMapping;

class Save extends AbstractAction
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Save constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LanguageMappingFactory $languageMappingFactory
     * @param LanguageMappingRepository $languageMappingRepository
     * @param DataPersistorInterface $dataPersistor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context                $context,
        Registry               $coreRegistry,
        LanguageMappingFactory         $languageMappingFactory,
        LanguageMappingRepository      $languageMappingRepository,
        DataPersistorInterface $dataPersistor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context, $coreRegistry, $languageMappingFactory, $languageMappingRepository);

        $this->dataPersistor = $dataPersistor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Execute the action
     *
     * @return ResultRedirect
     * @throws Exception
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        /** @var Http $request */
        $request = $this->getRequest();

        $data = $request->getPostValue();
        $postMagentoLocaleLanguage = $data[LanguageMapping::FIELD_MAGENTO_LOCALE_LANGUAGE];

        if (empty($data)) {
            return $resultRedirect;
        }

        $this->dataPersistor->set(LanguageMapping::CACHE_TAG, $data);

        // get the languageMapping id (if edit)
        $languageMappingId = null;
        if (!empty($data[LanguageMapping::FIELD_LANGUAGE_MAPPING_ID])) {
            $languageMappingId = (int) $data[LanguageMapping::FIELD_LANGUAGE_MAPPING_ID];
        }

        // load the languageMapping
        /** @var LanguageMapping $languageMapping */
        $languageMapping = $this->initModel($languageMappingId);
        $loadedMagentoLocaleLanguage = $languageMapping->getMagentoLocaleLanguage();
        $isMagentoLocaleLanguageUpdated = $postMagentoLocaleLanguage !== $loadedMagentoLocaleLanguage;

        // by default, redirect to the edit page of the languageMapping
        $resultRedirect->setPath(
            '*/*/edit',
            [LanguageMapping::FIELD_LANGUAGE_MAPPING_ID => $languageMappingId]
        );

        $languageMapping->populateFromArray($data);

        $languageMappingRepository = $this->getFilteredItem($data);

        try {
            // if we update loaded magento_locale_language without change it or if the locale selected doesnt exist
            if ((!$isMagentoLocaleLanguageUpdated && $languageMappingId !== null) ||
                $languageMappingRepository->getTotalCount() < 1
            ) {
                $this->languageMappingRepository->save($languageMapping);
                $this->messageManager->addSuccessMessage(__('The language mapping has been saved.'));
            } else {
                $this->messageManager->addErrorMessage(
                    __(
                        'Language Mapping with same magento locale already exists.' .
                        'Magento locales can be mapped only once.'
                    )
                );
            }

            if ($languageMappingId === null) {
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        LanguageMapping::FIELD_LANGUAGE_MAPPING_ID => $languageMapping->getNomenclatureId()
                    ]
                );
            }

            $this->dataPersistor->clear(LanguageMapping::CACHE_TAG);

            // if not go back => redirect to the list
            if (!$this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving the languageMapping. %1', $e->getMessage())
            );
        }

        return $resultRedirect;
    }

    /**
     * Check if an mapping already exists on textmaster_language_mapping table with same magento_locale_language
     * @param $data
     * @return LanguageMappingSearchResultsInterface
     */
    public function getFilteredItem($data)
    {
        $this->searchCriteriaBuilder->addFilter(
            LanguageMapping::FIELD_MAGENTO_LOCALE_LANGUAGE,
            $data[LanguageMapping::FIELD_MAGENTO_LOCALE_LANGUAGE],
            'eq'
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->languageMappingRepository->getList($searchCriteria);
    }
}
