<?php
/**
 * Admin Action : project/index
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
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use TextMaster\TextMaster\Helper\Project as ProjectHelper;

class Index extends AbstractAction
{
    /**
     * @var ProjectHelper
     */
    protected $projectHelper;

    /**
     * Index constructor.
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
     * @return ResultPage
     */
    public function execute()
    {
        $beingTranslatedProjects = $this->projectHelper->getBeingTranslatedProjects();

        foreach ($beingTranslatedProjects->getItems() as $project) {
            $this->projectHelper->resetExpiredProject($project);

            $beingTranslatedDocument = $this->projectHelper->getBeingTranslatedDocuments($project);
            if (count($beingTranslatedDocument->getItems()) > 0) {
                $this->messageManager->addNoticeMessage(
                    __(
                        'The translation on the project %1 is being to be applied. Please wait.',
                        $project->getName()
                    )
                );
            }
        }

        $breadMain = __('Manage Projects');

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('TextMaster_TextMaster::project');
        $resultPage->getConfig()->getTitle()->prepend($breadMain);

        return $resultPage;
    }
}
