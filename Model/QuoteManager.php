<?php

namespace Gabrielqs\Installments\Model;

use \Magento\Quote\Model\Quote;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Phrase;
use \Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class QuoteManager
 * @package Gabrielqs\Installments\Model
 */
class QuoteManager
{
    /**
     * Checkout Session
     * @var CheckoutSession
     */
    protected $_checkoutSession = null;

    /**
     * QuoteManager constructor.
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Installments Calculator
     * @var Calculator
     */
    protected $_calculator = null;

    /**
     * Returns quote total without interest amount
     * @return float
     */
    protected function _getBasePaymentAmount()
    {
        return $this->_getQuote()->getBaseGrandTotal() - $this->_getQuote()->getBaseGabrielqsInstallmentsInterestAmount();
    }

    /**
     * Installments Calculator Getter
     * @return Calculator
     */
    protected function _getCalculator()
    {
        return $this->_calculator;
    }

    /**
     * Returns quote total without interest amount
     * @return float
     */
    protected function _getPaymentAmount()
    {
        return $this->_getQuote()->getGrandTotal() - $this->_getQuote()->getGabrielqsInstallmentsInterestAmount();
    }

    /**
     * Retrieves quote from checkout session
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Installments Calculator Setter
     * @param Calculator $calculator
     * @return $this
     */
    public function setCalculator(Calculator $calculator)
    {
        $this->_calculator = $calculator;
        return $this;
    }

    /**
     * Sets interest info on order object with help from the installments order manager class
     * @param int $installmentQuantity
     * @return void
     * @throws LocalizedException
     */
    public function setInstallmentDataBeforeAuthorization($installmentQuantity)
    {
        if ($this->_calculator === null) {
            throw new LocalizedException(new Phrase('You need to set an installment calculator befor prior to' .
            'installments'));
        }

        /* @var Quote $quote */
        $quote = $this->_getQuote();
        $interestRate = $this->_getCalculator()->getInterestRateForInstallment($installmentQuantity);
        $interestAmount = $this->_getCalculator()->getInterestAmount($this->_getPaymentAmount(), $installmentQuantity);
        $baseinterestAmount = $this->_getCalculator()
            ->getInterestAmount($this->_getBasePaymentAmount(), $installmentQuantity);

        $quote
            ->setGabrielqsInstallmentsQty($installmentQuantity)
            ->setGabrielqsInstallmentsInterestRate($interestRate)
            ->setGabrielqsInstallmentsInterestAmount($interestAmount)
            ->setBaseGabrielqsInstallmentsInterestAmount($baseinterestAmount)
            ->setTotalsCollectedFlag(false)
            ->collectTotals();
    }
}