<?php
/**
 * Admin Action : project/applyTranslation
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\Registry;
use TextMaster\TextMaster\Api\Data\ProjectInterfaceFactory as ProjectFactory;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface   as ProjectRepository;
use Magento\Framework\View\LayoutFactory;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;
use Magento\Framework\MessageQueue\PublisherInterface;
use TextMaster\TextMaster\Api\MessageInterfaceFactory;
use TextMaster\TextMaster\Api\ConsumerInterface;

class ApplyTranslation extends AbstractAction
{
    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var MessageInterfaceFactory
     */
    protected $messageFactory;

    /**
     * ApplyTranslation constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ProjectFactory $projectFactory
     * @param ProjectRepository $projectRepository
     * @param LayoutFactory $layoutFactory
     * @param ConfigurationHelper $configurationHelper
     * @param ProjectHelper $projectHelper
     * @param PublisherInterface $publisher
     * @param MessageInterfaceFactory $messageFactory
     */
    public function __construct(
        Context                $context,
        Registry               $coreRegistry,
        ProjectFactory         $projectFactory,
        ProjectRepository      $projectRepository,
        LayoutFactory          $layoutFactory,
        ConfigurationHelper    $configurationHelper,
        ProjectHelper $projectHelper,
        PublisherInterface $publisher,
        MessageInterfaceFactory $messageFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $projectFactory,
            $projectRepository,
            $layoutFactory,
            $configurationHelper
        );

        $this->projectHelper = $projectHelper;
        $this->publisher = $publisher;
        $this->messageFactory = $messageFactory;
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

        $projectId = (int)$this->getRequest()->getParam(ProjectInterface::FIELD_PROJECT_ID);
        $selected = $this->getRequest()->getParam('selected');

        if (empty($projectId) && empty($selected)) {
            $this->messageManager->addErrorMessage(__('Error while applying translation.'));
            return $resultRedirect;
        }

        $selectedDocumentIds = [];
        if (!empty($selected)) {
            foreach ($selected as $selectedId) {
                $selectedDocumentIds[] = $selectedId;
            }
        }

        $project = $this->projectRepository->getById($projectId);

        $documentList = $this->projectHelper->getTranslatableDocuments($project, $selectedDocumentIds);

        foreach ($documentList->getItems() as $document) {
            $message = $this->messageFactory->create();
            $message->setDocumentId($document->getDocumentId());
            $message->setProjectId($project->getProjectId());

            $this->publisher->publish(
                ConsumerInterface::TOPIC_TEXTMASTER_APPLY_TRANSLATION,
                $message
            );

            if (empty($document->getStartTranslationAt())) {
                $this->projectHelper->setDocumentStartTranslationAt($document);
            }
        }

        if ($documentList->getTotalCount()) {
            $this->configurationHelper->getMessageManager()->addNoticeMessage(
                __('Application of translations in progress')
            );
            if (empty($project->getStartTranslationAt())) {
                $this->projectHelper->setProjectStartTranslationAt($project);
            }
        }

        return $resultRedirect;
    }
}
