<?php
/**
 * Abstract Admin action for language mapping
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Controller\Adminhtml\LanguageMapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use TextMaster\TextMaster\Api\Data\LanguageMappingInterfaceFactory as LanguageMappingFactory;
use TextMaster\TextMaster\Api\LanguageMappingRepositoryInterface   as LanguageMappingRepository;
use TextMaster\TextMaster\Api\Data\LanguageMappingInterface        as LanguageMapping;

abstract class AbstractAction extends Action
{
    /** @var Registry */
    protected $coreRegistry;

    /** @var LanguageMappingFactory */
    protected $languageMappingFactory;

    /** @var LanguageMappingRepository */
    protected $languageMappingRepository;

    /**
     * @param Context           $context
     * @param Registry          $coreRegistry
     * @param LanguageMappingFactory    $languageMappingFactory
     * @param LanguageMappingRepository $languageMappingRepository
     */
    public function __construct(
        Context           $context,
        Registry          $coreRegistry,
        LanguageMappingFactory    $languageMappingFactory,
        LanguageMappingRepository $languageMappingRepository
    ) {
        parent::__construct($context);

        $this->coreRegistry = $coreRegistry;
        $this->languageMappingFactory = $languageMappingFactory;
        $this->languageMappingRepository = $languageMappingRepository;
    }

    /**
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('TextMaster_TextMaster::configuration');
    }

    /**
     * Init the current languageMapping
     *
     * @param int|null $id
     *
     * @return LanguageMapping
     * @throws NotFoundException
     */
    protected function initModel($languageMappingId)
    {
        /** @var LanguageMapping $languageMapping */
        $languageMapping = $this->languageMappingFactory->create();

        // Initial checking
        if ($languageMappingId) {
            try {
                $languageMapping = $this->languageMappingRepository->getById($languageMappingId);
            } catch (NoSuchEntityException $e) {
                throw new NotFoundException(__('This language mapping does not exist'));
            }
        }

        // Register languageMapping to use later in blocks
        $this->coreRegistry->register('current_language_mapping', $languageMapping);

        return $languageMapping;
    }
}
