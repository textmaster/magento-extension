<?php
/**
 * Project Validate Quote
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\Project;

use TextMaster\TextMaster\Api\Data\ProjectInterface;
use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;
use TextMaster\TextMaster\Model\Connector\AcceptQuote;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface as ProjectRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ValidateQuote extends Action
{
    /**
     * @var ConnectorHelper
     */
    protected $connectorHelper;

    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * ValidateQuote constructor.
     * @param Context $context
     * @param ConnectorHelper $connectorHelper
     * @param ProjectRepository $projectRepository
     */
    public function __construct(
        Context $context,
        ConnectorHelper $connectorHelper,
        ProjectRepository $projectRepository
    ) {
        $this->connectorHelper = $connectorHelper;
        $this->projectRepository = $projectRepository;
        parent::__construct($context);
    }

    /**
     * @return ResultRedirect
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

        $projectId = (int) $this->getRequest()->getParam(ProjectInterface::FIELD_PROJECT_ID);

        if (empty($projectId)) {
            $this->messageManager->addErrorMessage(__('Error while validating the quote.'));
            return $resultRedirect;
        }

        try {
            $project = $this->projectRepository->getById($projectId);
            $this->connectorHelper->acceptQuote(
                $project->getTextMasterId(),
                AcceptQuote::QUOTE_STATUS_ACCEPTED
            );

            $project->setQuoteValidated(true);
            $this->projectRepository->save($project);
            $this->messageManager->addSuccessMessage(
                __('The quote of the project %1 has been validated', $project->getName())
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect;
    }
}
