<?php

namespace Gabrielqs\Installments\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Gabrielqs\Installments\Helper\Data as InstallmentsHelper;

class PaymentMethods implements OptionSourceInterface
{
    /**
     * Installments Helper
     * @var InstallmentsHelper
     */
    protected $_installmentsHelper = null;

    /**
     * PaymentMethods constructor.
     * @param InstallmentsHelper $_installmentsHelper
     */
    public function __construct(
        InstallmentsHelper $_installmentsHelper
    ) {
        $this->_installmentsHelper = $_installmentsHelper;
    }

    /**
     * Returns payment methods available for installment calculation
     * The methods must be declared...
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $return = [];

        $paymentMethodCodes = $this->_installmentsHelper->getAllInstallmentPaymentMethodCodes();
        foreach ($paymentMethodCodes as $paymentMethodCode) {
            $return[] = [
                'value' => $paymentMethodCode,
                'label' => ucfirst(str_replace('_', ' ', $paymentMethodCode))
            ];
        }
        return $return;
    }
}