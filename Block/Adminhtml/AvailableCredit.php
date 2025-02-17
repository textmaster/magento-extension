<?php
/**
 * Available Credit Block
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Block\Adminhtml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use TextMaster\TextMaster\Helper\Configuration as ConfigurationHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class AvailableCredit extends Template
{
    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var array|null
     */
    protected $information;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param ConfigurationHelper $configurationHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfigurationHelper $configurationHelper,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configurationHelper = $configurationHelper;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @return array
     *
     * @throws LocalizedException
     */
    protected function getInformation(): array
    {
        if (!isset($this->information)) {
            $this->information = $this->configurationHelper->getConnectorHelper()->getInformation();
        }
        return $this->information;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getAvailableCredit(): string
    {
        $availableCredit = '';
        $information = $this->getInformation();
        if (isset($information['wallet'])) {
            $availableCredit = $this->priceCurrency->format(
                $information['wallet']['current_money'],
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $information['wallet']['currency_code']
            );
        }
        return $availableCredit;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getEmail(): string
    {
        $email = '';
        $information = $this->getInformation();
        if (isset($information['email'])) {
            $email = $information['email'];
        }
        return $email;
    }

    /**
     * @return string
     */
    public function getPaymentRequestsUrl(): string
    {
        return $this->configurationHelper->getDataHelper()->getPaymentRequestsUrl();
    }
}
