<?php
/**
 * Admin Action : languagemapping/massDelete
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\LanguageMapping;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

class MassDelete extends AbstractAction
{
    /**
     * Execute the action
     *
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

        $languageMappingIds = $this->getRequest()->getParam('selected', []);
        try {
            if (!is_array($languageMappingIds) || count($languageMappingIds) < 1) {
                $languageMappings = $this->languageMappingRepository->getList();
                $languageMappingsSize = $languageMappings->getTotalCount();
                foreach ($languageMappings->getItems() as $item) {
                    $item->delete();
                }
                $this->messageManager->addSuccessMessage(
                    __('Total of %1 language mapping(s) were deleted.', $languageMappingsSize)
                );
                return $resultRedirect;
            }

            foreach ($languageMappingIds as $languageMappingId) {
                $this->languageMappingRepository->deleteById((int) $languageMappingId);
            }
            $this->messageManager->addSuccessMessage(
                __('Total of %1 language mapping(s) were deleted.', count($languageMappingIds))
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect;
    }
}
