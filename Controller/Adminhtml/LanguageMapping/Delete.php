<?php
/**
 * Language Mapping Delete
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\LanguageMapping;

use TextMaster\TextMaster\Model\LanguageMapping;
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
            $languageMappingId = (int) $this->getRequest()->getParam(LanguageMapping::FIELD_LANGUAGE_MAPPING_ID);
            $this->languageMappingRepository->deleteById($languageMappingId);
            $this->messageManager->addSuccessMessage(__('The language mapping has been deleted.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The language mapping to delete does not exist.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect;
    }
}
