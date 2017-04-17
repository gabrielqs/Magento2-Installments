<?php

namespace Gabrielqs\Installments\Model\Totals\Quote;

use \Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use \Magento\Quote\Model\QuoteValidator;
use \Magento\Quote\Model\Quote;
use \Magento\Quote\Api\Data\ShippingAssignmentInterface;
use \Magento\Quote\Model\Quote\Address\Total;


class InterestAmount extends AbstractTotal
{
    /**
     * Quote Validator
     * @var QuoteValidator
     */
    protected $quoteValidator = null;

    /**
     * Interest Total constructor.
     * @param QuoteValidator $quoteValidator
     */
    public function __construct(
        QuoteValidator $quoteValidator
    ) {
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * Collect grand total address amount
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $baseInterest = (float) $quote->getBaseGabrielqsInstallmentsInterestAmount();
        $interest = (float) $quote->getGabrielqsInstallmentsInterestAmount();

        $total->addTotalAmount('gabrielqs_installments_interest_amount', $interest);
        $total->addBaseTotalAmount('gabrielqs_installments_interest_amount', $baseInterest);

        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $baseInterest);
        $total->setGrandTotal($total->getGrandTotal() + $interest);

        return $this;
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Interest');
    }
}