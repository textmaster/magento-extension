<?php
/**
 * Project Edit
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use TextMaster\TextMaster\Model\Project;
use Exception;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;

class Edit extends AbstractAction
{
    /**
     * Execute the action
     *
     * @return ResultPage|ResultRedirect
     * @throws Exception
     */
    public function execute()
    {
        if (!$this->configurationHelper->checkConfiguration()) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        }

        $projectId = (int) $this->getRequest()->getParam(Project::FIELD_PROJECT_ID);
        $project = $this->initModel($projectId);

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $breadcrumbTitle = $project->getProjectId() ? __('Edit Project') : __('New Project');

        $resultPage
            ->setActiveMenu('TextMaster_TextMaster::textmaster_menu')
            ->addBreadcrumb(__('Project'), __('Project'))
            ->addBreadcrumb($breadcrumbTitle, $breadcrumbTitle);

        $resultPage->getConfig()->getTitle()->prepend(__('Manage Project'));
        $resultPage->getConfig()->getTitle()->prepend(
            $project->getProjectId()
                ? __("Edit Project #%1", $project->getProjectId())
                : __('New Project')
        );

        return $resultPage;
    }
}
