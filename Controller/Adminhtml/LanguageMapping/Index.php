<?php
/**
 * Language Mapping Index action
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\LanguageMapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use TextMaster\TextMaster\Helper\Data;

class Index extends Action implements HttpGetActionInterface
{
    const MENU_ID = 'TextMaster_TextMaster::configuration_language_mapping';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ConfigurationHelper $configurationHelper
     * @param ResultFactory $resultFactory
     * @param UrlInterface $urlBuilder
     * @param Data $dataHelper
     * @param MessageManager $messageManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ConfigurationHelper $configurationHelper,
        ResultFactory $resultFactory,
        UrlInterface $urlBuilder,
        Data $dataHelper,
        MessageManager $messageManager
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->configurationHelper = $configurationHelper;
        $this->resultFactory = $resultFactory;
        $this->urlBuilder = $urlBuilder;
        $this->dataHelper = $dataHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * Load the page defined in view/adminhtml/layout/textmaster_languagemapping_index.xml
     *
     */
    public function execute()
    {
        if (!$this->configurationHelper->hasApiKeyAndApiSecret()) {
            $this->messageManager->addComplexWarningMessage(
                'hasNoApiKeyOrApiSecretMessage',
                [
                    'url' => $this->urlBuilder->getUrl($this->dataHelper->getAuthentificationConfigurationUrl())
                ]
            );
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('textmaster/project/index');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Language Mapping Configuration'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('TextMaster_TextMaster::configuration');
    }
}
