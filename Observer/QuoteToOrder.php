<?php

namespace Gabrielqs\Installments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuoteToOrder
 * @package Gabrielqs\Installments\Observer
 */
class QuoteToOrder implements ObserverInterface
{
    /**
     * Copies gabrielqs_installments fields from Quote to Order
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        $order
            ->setGabrielqsInstallmentsQty($quote->getGabrielqsInstallmentsQty())
            ->setGabrielqsInstallmentsInterestRate($quote->getGabrielqsInstallmentsInterestRate())
            ->setGabrielqsInstallmentsInterestAmount($quote->getGabrielqsInstallmentsInterestAmount())
            ->setBaseGabrielqsInstallmentsInterestAmount($quote->getBaseGabrielqsInstallmentsInterestAmount());
    }
}

