<?php

namespace Gabrielqs\Installments\Block\MaximumInstallments;

use \Magento\Catalog\Block\Product\View\AbstractView;
use \Magento\Catalog\Block\Product\Context;
use \Magento\Framework\Stdlib\ArrayUtils;
use \Magento\Framework\DataObject;
use \Magento\Catalog\Model\Product as ProductModel;
use \Magento\Framework\Pricing\Helper\Data as PricingHelper;
use \Gabrielqs\Installments\Helper\Data as InstallmentsHelper;

class Product extends AbstractView
{
    /**
     * Installments Helper
     * @var InstallmentsHelper
     */
    protected $_installmentsHelper = null;

    /**
     * Maximum Installments Cache
     * @var DataObject[]
     */
    protected $_maximumInstallmentsCache = [];

    /**
     * Pricing Helper
     * @var PricingHelper
     */
    protected $_pricingHelper = null;

    /**
     * Constructor
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param InstallmentsHelper $installmentsHelper
     * @param PricingHelper $pricingHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        InstallmentsHelper $installmentsHelper,
        PricingHelper $pricingHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
        $this->_installmentsHelper = $installmentsHelper;
        $this->_pricingHelper = $pricingHelper;
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
     * Returns maximum instalment for a given $product
     * @param ProductModel $product
     * @return DataObject
     */
    public function getMaximumInstallment(ProductModel $product)
    {
        if (!array_key_exists($product->getId(), $this->_maximumInstallmentsCache)) {
            $maxInstallment = $this->_installmentsHelper->getMaximumInstallment($product->getFinalPrice());
            $this->_maximumInstallmentsCache[$product->getId()] = $maxInstallment;
        }
        return $this->_maximumInstallmentsCache[$product->getId()];
    }

    /**
     * Gets maximum installment amount for current product
     * @return int
     */
    public function getMaximumInstallmentAmount()
    {
        $maximumInstallment = $this->getMaximumInstallment($this->getProduct());
        return $maximumInstallment->getMaximumInstallmentsAmount();
    }

    /**
     * Gets maximum installment quantity for current product
     * @return int
     */
    public function getMaximumInstallmentQuantity()
    {
        $product = $this->getProduct() ? $this->getProduct() : $this->getParentBlock()->getProduct();
        $maximumInstallment = $this->getMaximumInstallment($product);
        return $maximumInstallment->getMaximumInstallmentsQuantity();
    }

    /**
     * Should we show the maximum installments on product list pages?
     * @return bool
     */
    public function isShowMaximumInstallments()
    {
        return $this->_installmentsHelper->isShowMaximumInstallmentsProductList();
    }
}