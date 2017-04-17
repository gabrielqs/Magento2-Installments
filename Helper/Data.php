<?php

namespace Gabrielqs\Installments\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\DataObjectFactory;
use \Magento\Framework\DataObject;
use \Gabrielqs\Installments\Model\Calculator;

class Data extends AbstractHelper
{
    /**
     * DataObjectFactory Factory
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory = null;

    /**
     * Helper Factory
     * @var ObjectManagerInterface
     * @todo Try not to use object manager here (kind of impossible)
     */
    protected $_helperFactory = null;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig = null;

    /**
     * Store manager interface
     *
     * @var StoreManagerInterface $_storeManager
     */
    protected $_storeManager = null;

    /**
     * Data constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $helperFactory
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $helperFactory,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_helperFactory = $helperFactory;
        $this->_dataObjectFactory = $dataObjectFactory;
        parent::__construct($context);
    }

    /**
     * Returns Cielo Payment Method System Config
     *
     * @param string $field
     * @param null $storeId
     * @return array|string
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->_storeManager->getStore(null);
        }
        $path = 'installments/' . $field;
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Returns all installment ready payment methods' codes
     * @return string[]
     */
    public function getAllInstallmentPaymentMethodCodes()
    {
        $return = [];
        $paymentMethods = (array) $this->_scopeConfig->getValue('installments/payment_methods');
        foreach ($paymentMethods as $code => $null) {
            $return[] = $code;
        }
        return $return;
    }

    /**
     * Get Installments Helper from Payment Method
     * @param string $methodCode
     * @return \Magento\Framework\App\Helper\AbstractHelper
     * @throws \LogicException
     */
    protected function _getInstallmentsHelperFromPaymentMethod($methodCode)
    {
        $helperClassName = $this->_scopeConfig->getValue('installments/payment_methods/'
            . $methodCode . '/installments_helper');
        $helper = $this->_helperFactory->get($helperClassName);
        if (false === $helper instanceof \Magento\Framework\App\Helper\AbstractHelper) {
            throw new \LogicException($helperClassName .
                ' doesn\'t extend Magento\Framework\App\Helper\AbstractHelper');
        }

        return $helper;
    }

    /**
     * Returns the object used in maximum installment calculation return
     * @param float $amount
     * @return DataObject
     */
    protected function _getMaximumInstallmentReturnDataObject($amount)
    {
        $return = $this->_dataObjectFactory->create();
        $return
            ->setMaximumInstallmentsQuantity(1)
            ->setMaximumInstallmentsAmount($amount);

        return $return;
    }

    /**
     * Returns the maximum installment for a given $amount. It might be calculated using interest free installments
     * only or not
     * @param float $amount
     * @param bool $interestFreeOnly
     * @return DataObject
     */
    public function getMaximumInstallment($amount, $interestFreeOnly = null)
    {
        $return = $this->_getMaximumInstallmentReturnDataObject($amount);

        if ($interestFreeOnly === null) {
            $interestFreeOnly = $this->isConsiderInterestFreeInstallmentsOnly();
        }

        foreach ($this->getAllInstallmentPaymentMethodCodes() as $code) {
            $installmentsHelper = $this->_getInstallmentsHelperFromPaymentMethod($code);
            /* @var Calculator $calculator */
            $calculator = $installmentsHelper->getInstallmentsCalculator();
            $installments = $calculator->getInstallments($amount);
            foreach ($installments as $installmentOption) {
                if ($interestFreeOnly && $installmentOption->interestsApplied) {
                    break;
                }
                if (
                    ($installmentOption->numberInstallments > $return->getMaximumInstallmentsQuantity()) ||
                    (
                        ($installmentOption->numberInstallments == $return->getMaximumInstallmentsQuantity()) &&
                        ($installmentOption->installmentValue > $return->getMaximumInstallmentsAmount())
                    )
                ) {
                    $return->setMaximumInstallmentsQuantity($installmentOption->numberInstallments);
                    $return->setMaximumInstallmentsAmount($installmentOption->installmentValue);
                }
            }
        }
        return $return;
    }

    /**
     * Should consider interest free installments only?
     * @return bool
     */
    public function isConsiderInterestFreeInstallmentsOnly()
    {
        return (bool) $this->getConfigData('maximum_installments/interest_free_only');
    }

    /**
     * Should show maximum installments on product lists?
     * @return bool
     */
    public function isShowMaximumInstallmentsProductList()
    {
        return (bool) $this->getConfigData('maximum_installments/enable_product_list');
    }

    /**
     * Should show maximum installments on product view pages?
     * @return bool
     */
    public function isShowMaximumInstallmentsProductView()
    {
        return (bool) $this->getConfigData('maximum_installments/enable_product_view');
    }

    /**
     * Should show maximum installments on shopping cart?
     * @return bool
     */
    public function isShowMaximumInstallmentsCart()
    {
        return (bool) $this->getConfigData('maximum_installments/enable_cart');
    }
}