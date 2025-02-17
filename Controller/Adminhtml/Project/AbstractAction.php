<?php
/**
 * Abstract Admin action for project
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use TextMaster\TextMaster\Api\Data\ProjectInterfaceFactory as ProjectFactory;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface   as ProjectRepository;
use TextMaster\TextMaster\Api\Data\ProjectInterface        as Project;
use Magento\Framework\View\LayoutFactory;

abstract class AbstractAction extends Action
{
    /** @var Registry */
    protected $coreRegistry;

    /** @var ProjectFactory */
    protected $projectFactory;

    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var LayoutFactory */
    protected $layoutFactory;

    /** @var ConfigurationHelper */
    protected $configurationHelper;

    /**
     * @param Context             $context
     * @param Registry            $coreRegistry
     * @param ProjectFactory      $projectFactory
     * @param ProjectRepository   $projectRepository
     * @param LayoutFactory       $layoutFactory
     * @param ConfigurationHelper $configurationHelper
     */
    public function __construct(
        Context             $context,
        Registry            $coreRegistry,
        ProjectFactory      $projectFactory,
        ProjectRepository   $projectRepository,
        LayoutFactory       $layoutFactory,
        ConfigurationHelper $configurationHelper
    ) {
        parent::__construct($context);

        $this->coreRegistry = $coreRegistry;
        $this->projectFactory = $projectFactory;
        $this->projectRepository = $projectRepository;
        $this->layoutFactory = $layoutFactory;
        $this->configurationHelper = $configurationHelper;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('TextMaster_TextMaster::project');
    }

    /**
     * Init the current project
     *
     * @param int|null $projectId
     *
     * @return Project
     * @throws NotFoundException
     */
    protected function initModel($projectId)
    {
        /** @var Project $project */
        $project = $this->projectFactory->create();

        // Initial checking
        if ($projectId) {
            try {
                $project = $this->projectRepository->getById($projectId);
                $project->setOrigData();
            } catch (NoSuchEntityException $e) {
                throw new NotFoundException(__('This project does not exist'));
            }
        }

        // Register project to use later in blocks
        $this->coreRegistry->register('current_project', $project);

        return $project;
    }
}
