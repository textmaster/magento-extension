<?php
/**
 * Language Mapping Delete Button
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Block\Adminhtml\Edit\LanguageMapping;

use TextMaster\TextMaster\Api\LanguageMappingRepositoryInterface;
use TextMaster\TextMaster\Model\LanguageMapping;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Block\Widget\Context;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var LanguageMappingRepositoryInterface
     */
    protected $languageMappingRepository;

    /**
     * @var Context
     */
    protected $context;

    /**
     * DeleteButton Constructor
     *
     * @param Context                            $context                   Context
     * @param Registry                           $registry                  Registry
     * @param LanguageMappingRepositoryInterface $languageMappingRepository Repository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LanguageMappingRepositoryInterface $languageMappingRepository
    ) {
        parent::__construct($context, $registry);
        $this->languageMappingRepository = $languageMappingRepository;
        $this->context = $context;
    }

    /**
     * @return int|null
     */
    protected function getId()
    {
        $itemId = $this->context->getRequest()->getParam(LanguageMapping::FIELD_LANGUAGE_MAPPING_ID);
        if ($itemId === null) {
            return null;
        }
        try {
            $languageMapping = $this->languageMappingRepository->getById((int) $itemId);
            return $languageMapping->getLanguageMappingId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getId()) {
            $message = $this
                ->context
                ->getEscaper()
                ->escapeJs(
                    __(
                        'Are you sure you want to delete this language mapping ?'
                    )
                );
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('{$message}', '{$this->getDeleteUrl()}')",
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', [LanguageMapping::FIELD_LANGUAGE_MAPPING_ID => $this->getId()]);
    }
}
