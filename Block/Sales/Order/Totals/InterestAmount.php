<?php

namespace Gabrielqs\Installments\Block\Sales\Order\Totals;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\DataObject;

/**
 * Class Interest
 * @package Gabrielqs\Installments\Block\Sales\Order
 */
class InterestAmount extends Template
{
    /**
     * Interest Constructor
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Retrieves interest amount from order
     * @return float
     */
    protected function _addTotal()
    {
        return (float) $this->getParentBlock()->getOrder()->getGabrielqsInstallmentsInterestAmount();
    }

    /**
     * Retrieves interest amount from order
     * @return float
     */
    protected function _getInterestAmount()
    {
        return (float) $this->getParentBlock()->getOrder()->getGabrielqsInstallmentsInterestAmount();
    }

    /**
     * Initialize all order totals relates with tax
     * @return $this
     */
    public function initTotals()
    {
        if ($value = $this->_getInterestAmount()) {
            $installmentInterest = new DataObject(
                [
                    'code' => 'gabrielqs_installments_interest_amount',
                    'strong' => false,
                    'value' => $value,
                    'label' => __('Interest'),
                    'class' => __('Interest'),
                ]
            );

            $this->getParentBlock()->addTotal($installmentInterest, 'gabrielqs_installments_interest_amount');
        }

        return $this;
    }
}