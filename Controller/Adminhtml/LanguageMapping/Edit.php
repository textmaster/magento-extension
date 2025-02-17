<?php
/**
 * Language Mapping Edit
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\LanguageMapping;

use TextMaster\TextMaster\Model\LanguageMapping;
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
        $languageMappingId = (int) $this->getRequest()->getParam(LanguageMapping::FIELD_LANGUAGE_MAPPING_ID);
        $languageMapping = $this->initModel($languageMappingId);

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $breadcrumbTitle = $languageMapping->getLanguageMappingId() ?
            __('Edit Language Mapping') : __('New Language Mapping');

        $resultPage
            ->setActiveMenu('TextMaster_TextMaster::textmaster_menu')
            ->addBreadcrumb(__('Language Mapping'), __('Language Mapping'))
            ->addBreadcrumb($breadcrumbTitle, $breadcrumbTitle);

        $resultPage->getConfig()->getTitle()->prepend(__('Manage Language Mapping'));
        $resultPage->getConfig()->getTitle()->prepend(
            $languageMapping->getLanguageMappingId()
                ? __("Edit Language Mapping #%1", $languageMapping->getLanguageMappingId())
                : __('New Language Mapping')
        );

        return $resultPage;
    }
}
