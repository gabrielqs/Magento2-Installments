<?php

namespace Gabrielqs\Installments\Test\Unit\Block\MaximumInstallments;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Block\MaximumInstallments\Cart as CartMaximumInstallmentsBlock;
use \Magento\Framework\Pricing\Helper\Data as PricingHelper;
use \Gabrielqs\Installments\Helper\Data as InstallmentsHelper;


/**
 * Cart Maximum Installments Block Unit Testcase
 */
class CartTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InstallmentsHelper
     */
    protected $installmentsHelper = null;

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var CartMaximumInstallmentsBlock
     */
    protected $originalObject = null;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper = null;

    /**
     * @var CartMaximumInstallmentsBlock
     */
    protected $subject = null;


    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = '\Gabrielqs\Installments\Block\MaximumInstallments\Cart';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['getCartGrandTotal'])
            ->getMock();

        $this->originalObject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->pricingHelper = $this
            ->getMockBuilder(PricingHelper::class)
            ->setMethods(['currency'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['pricingHelper'] = $this->pricingHelper;

        $this->installmentsHelper = $this
            ->getMockBuilder(InstallmentsHelper::class)
            ->setMethods(['getMaximumInstallment', 'isShowMaximumInstallmentsCart'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['installmentsHelper'] = $this->installmentsHelper;

        return $arguments;
    }

    public function testFormatCurrencyCallsPricingHelper()
    {
        $this
            ->pricingHelper
            ->expects($this->once())
            ->method('currency')
            ->with(239.40, true, false);

        $this
            ->originalObject
            ->formatCurrency(239.40);
    }

    public function testGetMaximumInstallmentGetsItFromHelperAndCheckoutSession()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getCartGrandTotal')
            ->will($this->returnValue(487.32));

        $this
            ->installmentsHelper
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->with(487.32)
            ->will($this->returnValue(true));

        # We're calling it twice on purpose
        # The block is supposed to compute it only once
        $this
            ->subject
            ->getMaximumInstallment();
        $this
            ->subject
            ->getMaximumInstallment();
    }

    public function testIsShowMaximumInstallmentsCallsHelper()
    {
        $this
            ->installmentsHelper
            ->expects($this->once())
            ->method('isShowMaximumInstallmentsCart');

        $this
            ->subject
            ->isShowMaximumInstallments();
    }

    public function testGetMaximumInstallmentAmountGetsTheRightValueFromMaximumInstallments()
    {
        # Can't use the class subject, because we need to mock a tested method getMaximumInstallment
        $subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($this->getConstructorArguments())
            ->setMethods(['getMaximumInstallment'])
            ->getMock();

        $dataObject = $this->objectManager->getObject('\Magento\Framework\DataObject');
        $dataObject->setMaximumInstallmentsAmount(102.30);
        $subject
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->will($this->returnValue($dataObject));

        $this->assertEquals($subject->getMaximumInstallmentAmount(), 102.30);
    }

    public function testGetMaximumInstallmentQuantityGetsTheRightValueFromMaximumInstallments()
    {
        # Can't use the class subject, because we need to mock a tested method getMaximumInstallment
        $subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($this->getConstructorArguments())
            ->setMethods(['getMaximumInstallment'])
            ->getMock();

        $dataObject = $this->objectManager->getObject('\Magento\Framework\DataObject');
        $dataObject->setMaximumInstallmentsQuantity(4);
        $subject
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->will($this->returnValue($dataObject));

        $this->assertEquals($subject->getMaximumInstallmentQuantity(), 4);
    }
}