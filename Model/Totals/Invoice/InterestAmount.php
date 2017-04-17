<?php

namespace Gabrielqs\Installments\Model\Totals\Invoice;

use \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use \Magento\Sales\Model\Order\Invoice;


class InterestAmount extends AbstractTotal
{
    /**
     * Collect grand total  amount
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        parent::collect($invoice);

        $baseInterest = (float) $invoice->getBaseGabrielqsInstallmentsInterestAmount();
        $interest = (float) $invoice->getGabrielqsInstallmentsInterestAmount();
        if ($baseInterest) {
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseInterest);
            $invoice->setGrandTotal($invoice->getGrandTotal() + $interest);
        }

        return $this;
    }
}