<?php
/**
 * Admin Action : project/view
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use TextMaster\TextMaster\Api\Data\ProjectInterfaceFactory as ProjectFactory;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface as ProjectRepository;
use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Backend\Model\View\Result\Page       as ResultPage;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;

class View extends AbstractAction
{
    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * View constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ProjectFactory $projectFactory
     * @param ProjectRepository $projectRepository
     * @param LayoutFactory $layoutFactory
     * @param ConfigurationHelper $configurationHelper
     * @param ProjectHelper $projectHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ProjectFactory $projectFactory,
        ProjectRepository $projectRepository,
        LayoutFactory $layoutFactory,
        ConfigurationHelper $configurationHelper,
        ProjectHelper $projectHelper
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
    }

    /**
     * Execute the action
     *
     * @return ResultPage|ResultRedirect
     * @throws Exception
     */
    public function execute()
    {
        $projectId = (int) $this->getRequest()->getParam('project_id');
        $project = $this->initModel($projectId);

        $this->projectHelper->resetExpiredProject($project);
        $beingTranslatedDocuments = $this->projectHelper->getBeingTranslatedDocuments($project);
        if (count($beingTranslatedDocuments->getItems()) > 0) {
            $this->messageManager->addNoticeMessage(
                __('The translation on the project %1 is being to be applied. Please wait.', $project->getName())
            );
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $breadcrumbTitle = __('View Project');

        $resultPage
            ->setActiveMenu('TextMaster_TextMaster::project')
            ->addBreadcrumb(__('Projects'), __('Projects'))
            ->addBreadcrumb($breadcrumbTitle, $breadcrumbTitle);

        $resultPage->getConfig()->getTitle()->prepend(__('Manage Projects'));
        $resultPage->getConfig()->getTitle()->prepend(__("View Project #%1", $project->getName()));

        return $resultPage;
    }
}
