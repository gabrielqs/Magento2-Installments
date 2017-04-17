<?php

namespace Gabrielqs\Installments\Model;

use \Magento\Framework\DataObjectFactory;
use \Magento\Framework\DataObject;

/**
 * Class Installments Calculator
 *
 * Computes installment values and interest amounts.
 *
 * @package Gabrielqs|Installments|Model
 */
class Calculator
{

    /**
     * Data Object Factory
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory = null;

    /**
     * Insterest Rate
     * @var float
     */
    protected $_interestRate = null;

    /**
     * Payment amount
     * @var float
     */
    protected $_paymentAmount = null;

    /**
     * Maximum Installment Quantity
     * @var float
     */
    protected $_maximumInstallmentQuantity = null;

    /**
     * Minimum Installment Amount
     * @var float[]
     */
    protected $_minimumAmountNoInterest = [];

    /**
     * Minimum Installment Amount
     * @var float
     */
    protected $_minimumInstallmentAmount = null;

    /**
     * Calculator constructor.
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory
    ) {
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Returns all the information that another class might need to compute interest rates and installments
     * @return DataObject
     */
    public function getInstallmentConfig()
    {
        $installmentConfig = $this->_dataObjectFactory->create();

        $maxInstallmentQty = $this->getMaximumInstallmentQuantity();
        $installmentConfig->maximumInstallmentQty = $maxInstallmentQty;
        $installmentConfig->minimumInstallmentAmount = $this->getMinimumInstallmentAmount();
        $installmentConfig->interestRate = $this->getInterestRate();

        $installments = [];

        # Looping through all possible installment values
        for ($curInstallment = 1; ($curInstallment <= 12 && $curInstallment <= $maxInstallmentQty); $curInstallment++) {
            if ($curInstallment === 1) {
                # If this is a one time payment, we use the current value and only one installment, no interest
                $installments[$curInstallment] = $this->_dataObjectFactory->create();
                $installments[$curInstallment]->interestRate = $this->getInterestRateForInstallment($curInstallment);
                $installments[$curInstallment]->numberInstallments = 1;
                $installments[$curInstallment]->minimumAmountNoInterest = null;

                continue;
            } else {
                $installments[$curInstallment] = $this->_dataObjectFactory->create();
                $installments[$curInstallment]->interestRate = $this->getInterestRateForInstallment($curInstallment);
                $installments[$curInstallment]->numberInstallments = $curInstallment;
                $installments[$curInstallment]->minimumAmountNoInterest =
                    $this->getMinimumAmountNoInterest($curInstallment);
            }
        }
        $installmentConfig->installments = $installments;

        return $installmentConfig;
    }

    /**
     * Computes the installments for a given payment amount
     * @param float $paymentAmount
     * @return DataObject[]
     */
    public function getInstallments($paymentAmount)
    {
        $installments = [];

        $this->setPaymentAmount($paymentAmount);
        $maxInstallmentQty = $this->getMaximumInstallmentQuantity();

        # Looping through all possible installment values
        for ($curInstallment = 1; ($curInstallment <= 12 && $curInstallment <= $maxInstallmentQty); $curInstallment++) {

            if ($curInstallment === 1) {
                # If this is a one time payment, we use the current value and only one installment, no interest
                $installments[$curInstallment] = $this->_dataObjectFactory->create();
                $installments[$curInstallment]->installmentValue = $paymentAmount;
                $installments[$curInstallment]->numberInstallments = 1;
                $installments[$curInstallment]->interestsApplied = false;

                continue;
            } else {
                $totalAmountAfterInterest = $this->getTotalAmountAfterInterest($curInstallment);
                $amountPerInstallment = $totalAmountAfterInterest / $curInstallment;

                # If the total per installment is less then the minimum installment amount, we won't
                # include this installment
                $minimumInstallmentAmount = $this->getMinimumInstallmentAmount();
                if ($amountPerInstallment < $minimumInstallmentAmount) {
                    continue;
                }

                $installments[$curInstallment] = $this->_dataObjectFactory->create();
                $installments[$curInstallment]->installmentValue = $amountPerInstallment;
                $installments[$curInstallment]->numberInstallments = $curInstallment;
                $installments[$curInstallment]->interestsApplied = $this->isApplyInterest($curInstallment);
            }
        }

        return (array) $installments;
    }

    /**
     * Returns the interest fee for a given amount and interest rate
     * @param float $amount
     * @param int $installmentQuantity
     * @return float
     */
    public function getInterestAmount($amount, $installmentQuantity)
    {
        $minimumAmountNoInterest = $this->getMinimumAmountNoInterest($installmentQuantity);

        if (
            ($minimumAmountNoInterest === null) ||
            ($minimumAmountNoInterest !== null) && ($amount < $minimumAmountNoInterest)
        ) {
            $interestRateForInstallment = $this->getInterestRateForInstallment($installmentQuantity);
        } else {

            $interestRateForInstallment = 1;
        }

        $totalInstallmentAmount = $interestRateForInstallment * $amount;
        return ($totalInstallmentAmount - $amount);
    }

    /**
     * Payment Interest Rate Getter
     * @return float
     */
    public function getInterestRate()
    {
        return $this->_interestRate;
    }

    /**
     * Given a number of installments, returns the total interest rate do be applied
     * @param int $installments
     * @return float
     */
    public function getInterestRateForInstallment($installments)
    {
        $interestRate = $this->getInterestRate();
        $computationInstallments = ($installments - 1);
        $totalInterestRate = (float) pow($interestRate, $computationInstallments);
        return $totalInterestRate;
    }

    /**
     * Maximum Installment Quantity Getter
     * @return float
     */
    public function getMaximumInstallmentQuantity()
    {
        return $this->_maximumInstallmentQuantity;
    }

    /**
     * Gets the minimum amount for which, in the specified installment qty, no interest should apply
     * @param int $installments
     * @return float|null
     */
    public function getMinimumAmountNoInterest($installments)
    {
        $return = null;

        foreach ($this->_minimumAmountNoInterest as $installmentQty => $minOrderValue) {
            if ($installmentQty == $installments) {
                $return = (float) $minOrderValue;
                break;
            }
        }

        return $return;
    }

    /**
     * Minimum Installment Amount Getter
     * @return float
     */
    public function getMinimumInstallmentAmount()
    {
        return $this->_minimumInstallmentAmount;
    }

    /**
     * Payment Amount Getter
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->_paymentAmount;
    }

    /**
     * Computes the maximum amount after interest is applied
     * @param int $installments
     * @return float
     */
    public function getTotalAmountAfterInterest($installments)
    {
        $return = $amount = $this->getPaymentAmount();

        if ($this->isApplyInterest($installments)) {
            $return = ($amount) * $this->getInterestRateForInstallment($installments);
        }

        return $return;
    }

    /**
     * Decides whether interest should be applied to the current payment
     * @param int $installments
     * @return bool $return
     */
    public function isApplyInterest($installments)
    {
        $return = true;

        $interestRate = $this->getInterestRate();
        $paymentAmount = $this->getPaymentAmount();

        if (($installments > 1) && ($interestRate > 1)) {
            # If we're not dealing with a one time payment and the interest rate is defined, interest will always be
            # applied, except when the payment total is higher than the minimum order value for no interest setting
            $minimumOrderValueNoInterest = $this->getMinimumAmountNoInterest($installments);
            if ( ($paymentAmount > $minimumOrderValueNoInterest) && ($minimumOrderValueNoInterest !== null) ) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Interest Rate Setter - Expects the rate to be informed in the multiplier form. For example, if the interest
     * rate is 1.99%, 1.0199 should be informed
     * @param float $value
     * @return $this
     */
    public function setInterestRate($value)
    {
        $this->_interestRate = (float) $value;
        return $this;
    }

    /**
     * Maximum Installment Quantity Setter
     * @param float $value
     * @return $this
     */
    public function setMaximumInstallmentQuantity($value)
    {
        $this->_maximumInstallmentQuantity = (float) $value;
        return $this;
    }

    /**
     * Minimum Amount for which an installment has no interest applied Setter
     * An array in the form array[$installmentQty] => $minAmount is expected. It doesn't need to be in any
     * specified order, as the setter sorts it internally.
     * @param float[] $value
     * @return $this
     */
    public function setMinimumAmountNoInterest($value)
    {
        $value = (array) $value;
        asort($value);
        $this->_minimumAmountNoInterest = $value;
        return $this;
    }

    /**
     * Minimum Installment Amount Setter
     * @param float $value
     * @return $this
     */
    public function setMinimumInstallmentAmount($value)
    {
        $this->_minimumInstallmentAmount = (float) $value;
        return $this;
    }

    /**
     * Payment Amount Setter
     * @param float $value
     * @return $this
     */
    public function setPaymentAmount($value)
    {
        $this->_paymentAmount = (float) $value;
        return $this;
    }

}