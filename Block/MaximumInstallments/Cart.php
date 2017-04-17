<?php

namespace Gabrielqs\Installments\Block\MaximumInstallments;

use \Magento\Catalog\Block\Product\Context;
use \Magento\Framework\DataObject;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Framework\Pricing\Helper\Data as PricingHelper;
use \Gabrielqs\Installments\Helper\Data as InstallmentsHelper;

class Cart extends \Magento\Framework\View\Element\Template
{
    /**
     * Checkout Session
     * @var CheckoutSession
     */
    protected $_checkoutSession = null;

    /**
     * Installments Helper
     * @var InstallmentsHelper
     */
    protected $_installmentsHelper = null;

    /**
     * Maximum Installments Cache
     * @var DataObject
     */
    protected $_maximumInstallment = null;

    /**
     * Pricing Helper
     * @var PricingHelper
     */
    protected $_pricingHelper = null;

    /**
     * Constructor
     * @param Context $context
     * @param InstallmentsHelper $installmentsHelper
     * @param PricingHelper $pricingHelper
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        InstallmentsHelper $installmentsHelper,
        PricingHelper $pricingHelper,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_installmentsHelper = $installmentsHelper;
        $this->_pricingHelper = $pricingHelper;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Formats amount into currency
     * @param float $amount
     * @return float|string
     */
    public function formatCurrency($amount)
    {
        return $this->_pricingHelper->currency($amount, true, false);
    }

    /**
     * Retrieves Grand Total from Checkout Session
     * @return float
     */
    public function getCartGrandTotal()
    {
        return (float) $this->_checkoutSession->getQuote()->getGrandTotal();
    }

    /**
     * Returns maximum instalment for the currently active shopping cart
     * @return DataObject
     */
    public function getMaximumInstallment()
    {
        if ($this->_maximumInstallment === null) {
            $grandTotal = $this->getCartGrandTotal();
            $this->_maximumInstallment = $this->_installmentsHelper->getMaximumInstallment($grandTotal);
        }
        return $this->_maximumInstallment;
    }

    /**
     * Gets maximum installment amount for current product
     * @return int
     */
    public function getMaximumInstallmentAmount()
    {
        $maximumInstallment = $this->getMaximumInstallment();
        return $maximumInstallment->getMaximumInstallmentsAmount();
    }

    /**
     * Gets maximum installment quantity for current product
     * @return int
     */
    public function getMaximumInstallmentQuantity()
    {
        $maximumInstallment = $this->getMaximumInstallment();
        return $maximumInstallment->getMaximumInstallmentsQuantity();
    }

    /**
     * Should we show the maximum installments on product list pages?
     * @return bool
     */
    public function isShowMaximumInstallments()
    {
        return $this->_installmentsHelper->isShowMaximumInstallmentsCart();
    }
}