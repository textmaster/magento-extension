<?php
/**
 * Admin Action : project/save
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use TextMaster\TextMaster\Api\Data\DocumentInterface;
use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Api\Data\TranslatableContentInterface;
use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Oauth\Helper\Oauth as OauthHelper;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use TextMaster\TextMaster\Api\Data\ProjectInterfaceFactory as ProjectFactory;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface   as ProjectRepository;
use TextMaster\TextMaster\Api\Data\DocumentInterfaceFactory as DocumentFactory;
use TextMaster\TextMaster\Api\DocumentRepositoryInterface   as DocumentRepository;
use TextMaster\TextMaster\Api\Data\ProjectAttributeInterfaceFactory as ProjectAttributeFactory;
use TextMaster\TextMaster\Api\ProjectAttributeRepositoryInterface   as ProjectAttributeRepository;
use TextMaster\TextMaster\Model\Project;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

class Save extends AbstractAction
{
    /** @var DataPersistorInterface */
    protected $dataPersistor;

    /** @var DocumentFactory */
    protected $documentFactory;

    /** @var DocumentRepository */
    protected $documentRepository;

    /** @var ProjectAttributeFactory */
    protected $projectAttributeFactory;

    /** @var ProjectAttributeRepository */
    protected $projectAttributeRepository;

    /** @var SearchCriteriaBuilderFactory */
    protected $searchCriteriaBuilderFactory;

    /** @var CategoryListInterface */
    protected $categoryList;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var OauthHelper */
    protected $oauthHelper;

    /**
     * @param Context                      $context
     * @param Registry                     $coreRegistry
     * @param ProjectFactory               $projectFactory
     * @param ProjectRepository            $projectRepository
     * @param DocumentFactory              $documentFactory
     * @param DocumentRepository           $documentRepository
     * @param ProjectAttributeFactory      $projectAttributeFactory
     * @param ProjectAttributeRepository   $projectAttributeRepository
     * @param LayoutFactory                $layoutFactory
     * @param DataPersistorInterface       $dataPersistor
     * @param CategoryListInterface        $categoryList
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SerializerInterface          $serializer
     * @param ConfigurationHelper          $configurationHelper
     * @param OauthHelper                  $oauthHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ProjectFactory $projectFactory,
        ProjectRepository $projectRepository,
        DocumentFactory $documentFactory,
        DocumentRepository $documentRepository,
        ProjectAttributeFactory $projectAttributeFactory,
        ProjectAttributeRepository $projectAttributeRepository,
        LayoutFactory $layoutFactory,
        DataPersistorInterface $dataPersistor,
        CategoryListInterface $categoryList,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SerializerInterface $serializer,
        ConfigurationHelper $configurationHelper,
        OauthHelper $oauthHelper
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $projectFactory,
            $projectRepository,
            $layoutFactory,
            $configurationHelper
        );

        $this->dataPersistor = $dataPersistor;
        $this->documentFactory = $documentFactory;
        $this->documentRepository = $documentRepository;
        $this->projectAttributeFactory = $projectAttributeFactory;
        $this->projectAttributeRepository = $projectAttributeRepository;
        $this->categoryList = $categoryList;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->serializer = $serializer;
        $this->oauthHelper = $oauthHelper;
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
        if (empty($data)) {
            return $resultRedirect;
        }

        $this->dataPersistor->set('textmaster_project', $data);

        // get the project id (if edit)
        $projectId = null;
        if (!empty($data[Project::FIELD_PROJECT_ID])) {
            $projectId = (int) $data[Project::FIELD_PROJECT_ID];
        }

        // load the project
        /** @var Project $project */
        $project = $this->initModel($projectId);

        // by default, redirect to the edit page of the project
        $resultRedirect->setPath(
            '*/*/edit',
            [Project::FIELD_PROJECT_ID => $projectId]
        );

        if (!array_key_exists('targets', $data)) {
            $this->messageManager->addErrorMessage(
                __('You must add at least one Target language and one API Template')
            );

            return $resultRedirect;
        }

        $this->dataProcessing($data);

        $project->populateFromArray($data);

        $nbProjects = count($data['targets']);

        $errorMessage = false;

        // check if at least one item to translate has been selected
        if ((int)$data[Project::FIELD_NUMBER_OF_DOCUMENTS] < 1) {
            $this->messageManager->addErrorMessage(
                __(
                    'You must select at least one %1 to translate',
                    $data[Project::FIELD_DOCUMENT_TYPE]
                )
            );
            $errorMessage = true;
        }

        // check if target languages or api templates are uniques
        // (prevent duplicating project or mismatching languages and templates)
        if ($nbProjects > 1 && $this->checkDuplicatePairs($data['targets'])) {
            $errorMessage = true;
        }

        if ($errorMessage === true) {
            return $resultRedirect;
        }

        // try to save it
        try {
            foreach ($data['targets'] as $target) {
                $newProject = clone $project;
                $newProject->setProjectType('translation');
                $newProject->setStatus(ProjectInterface::STATUS_IN_CREATION);
                $newProject->setToken($this->oauthHelper->generateRandomString(10));
                $newProject->setSourceLanguage($target[Project::FIELD_SOURCE_LANGUAGE]);
                $newProject->setLanguageLevel($target[Project::FIELD_LANGUAGE_LEVEL]);
                $newProject->setTargetLanguage($target[Project::FIELD_TARGET_LANGUAGE]);
                $newProject->setTargetStoreId((int) $target[Project::FIELD_TARGET_STORE_ID]);
                $newProject->setTemplateId($target[Project::FIELD_TEMPLATE_ID]);

                // 1. Create project in TMS with API call
                $createProjectResponse = $this->configurationHelper->getConnectorHelper()->createProject($newProject);

                if (isset($createProjectResponse['id'])) {
                    // 2. Set textmaster_id from TMS create project response on $newProject before save
                    $newProject->setTextMasterId($createProjectResponse['id']);
                    if (isset($createProjectResponse['autoLaunch'])) {
                        $newProject->setAutolaunch($createProjectResponse['autoLaunch']);
                    }
                    // 3. Save project in Magento since it has been created in TMS
                    $this->projectRepository->save($newProject);

                    // 4. Save project attribute codes in magento and get attribute codes list
                    $projectAttributeCodes = $this->assignProjectToAttributes($newProject, $data);
                    // 5. Create documents in TMS with API call
                    $this->assignProjectToDocuments($newProject, $data, $projectAttributeCodes);

                    // 6. Analyse project when all documents are created. Project is finalized
                    $analysisResponse = $this->configurationHelper->getConnectorHelper()->analysis(
                        $createProjectResponse['id']
                    );
                }
            }

            // display success message
            if ($nbProjects === 1) {
                $this->messageManager->addSuccessMessage(__('The project has been saved.'));
            }
            if ($nbProjects > 1) {
                $this->messageManager->addSuccessMessage(__('%1 projects have been saved.', $nbProjects));
            }
            $this->dataPersistor->clear('textmaster_project');

            // if not go back => redirect to the list
            if (!$this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving the project. %1', $e->getMessage())
            );
        }

        return $resultRedirect;
    }

    /**
     * @param array $targets
     *
     * @return bool
     */
    public function checkDuplicatePairs(array $targets): bool
    {
        $targetStoreIds = [];
        $templateIds = [];
        $isDuplicate = false;

        foreach ($targets as $target) {
            $targetStoreIds[] = $target[Project::FIELD_TARGET_STORE_ID];
            $templateIds[] = $target[Project::FIELD_TEMPLATE_ID];
        }
        if (count(array_unique($targetStoreIds)) < count($targetStoreIds)) {
            $this->messageManager->addErrorMessage(
                __(
                    'Target languages could not be the same. Source and target pairs should be unique.'
                )
            );
            $isDuplicate = true;
        }
        if (count(array_unique($templateIds)) < count($templateIds)) {
            $this->messageManager->addErrorMessage(
                __(
                    'Templates could not be the same.' .
                    ' Please check source and language pairs match appropriate template'
                )
            );
            $isDuplicate = true;
        }
        return $isDuplicate === true;
    }

    /**
     * @param Project $project
     * @param array $data
     * @param array $projectAttributeCodes
     * @throws CouldNotSaveException
     */
    protected function assignProjectToDocuments(Project $project, array $data, array $projectAttributeCodes)
    {
        $key = $project->getDocumentType() . '_ids';
        if (isset($data[$key]) && is_array($data[$key])) {
            foreach ($data[$key] as $magentoEntityId => $name) {
                $document = $this->documentFactory->create();
                $document->setStatus(DocumentInterface::STATUS_IN_CREATION);
                $document->setName($name);
                $document->setToken($this->oauthHelper->generateRandomString(10));
                $document->setProjectId($project->getProjectId());
                $document->setMagentoEntityId($magentoEntityId);

                // Create document in TMS with API call one by one
                $createDocumentResponse = $this->configurationHelper->getConnectorHelper()->createDocument(
                    $project,
                    $document,
                    $projectAttributeCodes
                );
                // if at least one attribute code selected exists for document,
                // we get response from TMS and save document in Magento
                if (isset($createDocumentResponse[0]['id'])) {
                    $document->setTextMasterId($createDocumentResponse[0]['id']);
                    $this->documentRepository->save($document);
                }
            }
        }
    }

    /**
     * @param Project $project
     * @param array $data
     * @return array
     * @throws CouldNotSaveException
     */
    protected function assignProjectToAttributes(Project $project, array $data)
    {
        $key = $project->getDocumentType() . '_attributes';
        $projectAttributeCodes = [];

        if (isset($data[$key]) && is_array($data[$key])) {
            foreach ($data[$key] as $attributeCode) {
                $projectAttribute = $this->projectAttributeFactory->create();
                $projectAttribute->setProjectId($project->getProjectId());
                $projectAttribute->setAttributeCode($attributeCode);
                $this->projectAttributeRepository->save($projectAttribute);
                $projectAttributeCodes[] = $projectAttribute->getAttributeCode();
            }
        }

        return $projectAttributeCodes;
    }

    /**
     * @param array $data
     */
    protected function dataProcessing(array &$data)
    {
        $numberOfDocuments = 0;
        if ($data[Project::FIELD_DOCUMENT_TYPE] === TranslatableContentInterface::DOCUMENT_TYPE_CATEGORY &&
            isset($data['category_ids']) &&
            is_array($data['category_ids'])
        ) {
            $categoryIds = [];
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder->addFilter('entity_id', $data['category_ids'], 'in');
            $searchCriteria = $searchCriteriaBuilder->create();
            $categoryList = $this->categoryList->getList($searchCriteria);
            if ($categoryList->getTotalCount()) {
                foreach ($categoryList->getItems() as $category) {
                    $categoryIds[$category->getId()] = $category->getName();
                }
            }
            $data['category_ids'] = $categoryIds;
            $numberOfDocuments = count($data['category_ids']);
        }

        $documentTypes = [
            TranslatableContentInterface::DOCUMENT_TYPE_PRODUCT,
            TranslatableContentInterface::DOCUMENT_TYPE_PAGE,
            TranslatableContentInterface::DOCUMENT_TYPE_BLOCK
        ];
        foreach ($documentTypes as $documentType) {
            if ($data[Project::FIELD_DOCUMENT_TYPE] === $documentType && isset($data[$documentType . '_ids'])) {
                $data[$documentType . '_ids'] = $this->serializer->unserialize($data[$documentType . '_ids']);
                $numberOfDocuments = count($data[$documentType . '_ids']);
                break;
            }
        }

        $fields = [
            Project::FIELD_SOURCE_LANGUAGE,
            Project::FIELD_TARGET_LANGUAGE,
            Project::FIELD_LANGUAGE_LEVEL
        ];
        foreach ($data['targets'] as $targetKey => $target) {
            foreach ($fields as $dataKey) {
                $data['targets'][$targetKey][$dataKey] = $this->configurationHelper->getTemplateData(
                    $target[Project::FIELD_TEMPLATE_ID],
                    $dataKey
                );
            }
        }

        $data[Project::FIELD_NUMBER_OF_DOCUMENTS] = $numberOfDocuments;
    }
}
