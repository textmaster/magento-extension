<?php
/**
 * Admin Action : project/delete
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use Exception;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends AbstractAction
{
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
        $resultRedirect->setPath('*/*/index');

        try {
            $projectId = (int) $this->getRequest()->getParam('project_id');
            $this->projectRepository->deleteById($projectId);
            $this->messageManager->addSuccessMessage(__('The project has been deleted.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The project to delete does not exist.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect;
    }
}
