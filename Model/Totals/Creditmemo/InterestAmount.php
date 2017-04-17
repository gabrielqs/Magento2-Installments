<?php

namespace Gabrielqs\Installments\Model\Totals\Creditmemo;

use \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use \Magento\Sales\Model\Order\Creditmemo;


class InterestAmount extends AbstractTotal
{
    /**
     * Collect grand total  amount
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);

        $order = $creditmemo->getOrder();
        $baseInterest = (float) $order->getBaseGabrielqsInstallmentsInterestAmount();
        $interest = (float) $order->getGabrielqsInstallmentsInterestAmount();
        if ($baseInterest) {
            $creditmemo->setBaseGabrielqsInstallmentsInterestAmount($baseInterest);
            $creditmemo->setGabrielqsInstallmentsInterestAmount($interest);

            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseInterest);
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $interest);
        }

        return $this;
    }
}