<?php
/**
 * Store Language Option Source
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class StoreLanguage implements OptionSourceInterface
{
    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param StoreRepositoryInterface $storeRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOptions(): array
    {
        $options = [];
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() != Store::DEFAULT_STORE_ID) {
                $lang = $this->scopeConfig->getValue(
                    'general/locale/code',
                    ScopeInterface::SCOPE_STORE,
                    $store
                );
                $options[$store->getId()] = $store->getWebsite()->getName() . ' - ' . $store->getName() . ' - ' . $lang;
            }
        }

        return $options;
    }
}
