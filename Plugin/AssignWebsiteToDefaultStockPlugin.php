<?php
/**
 * AssignWebsiteToDefaultStock Plugin
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */
declare(strict_types=1);

namespace TextMaster\TextMaster\Plugin;

use Magento\InventorySales\Setup\Operation\AssignWebsiteToDefaultStock;
use Magento\Store\Model\StoreManagerInterface;

class AssignWebsiteToDefaultStockPlugin
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Reinit Stores
     *
     * @param AssignWebsiteToDefaultStock $subject
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(AssignWebsiteToDefaultStock $subject)
    {
        $this->storeManager->reinitStores();
    }
}
