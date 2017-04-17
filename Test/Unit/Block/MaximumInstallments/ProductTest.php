<?php

namespace Gabrielqs\Installments\Test\Unit\Block\MaximumInstallments;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Block\MaximumInstallments\Product as ProductMaximumInstallmentsBlock;
use \Magento\Framework\Pricing\Helper\Data as PricingHelper;
use \Gabrielqs\Installments\Helper\Data as InstallmentsHelper;
use \Magento\Catalog\Model\Product;


/**
 * Cart Maximum Installments Block Unit Testcase
 */
class ProductTest extends \PHPUnit_Framework_TestCase
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
     * @var ProductMaximumInstallmentsBlock
     */
    protected $originalObject = null;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper = null;

    /**
     * @var ProductMaximumInstallmentsBlock
     */
    protected $subject = null;


    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = '\Gabrielqs\Installments\Block\MaximumInstallments\Product';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(null)
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
            ->setMethods(['getMaximumInstallment', 'isShowMaximumInstallmentsProductList'])
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


    public function testIsShowMaximumInstallmentsCallsHelper()
    {
        $this
            ->installmentsHelper
            ->expects($this->once())
            ->method('isShowMaximumInstallmentsProductList');

        $this
            ->subject
            ->isShowMaximumInstallments();
    }



    public function testGetMaximumInstallmentGetsItFromHelperAndProduct()
    {
        $this
            ->installmentsHelper
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->with(25.50)
            ->will($this->returnValue(true));

        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFinalPrice', 'getId'])
            ->getMock();
        $product
            ->expects($this->once())
            ->method('getFinalPrice')
            ->will($this->returnValue(25.50));
        $product
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(63));


        # We're calling it twice on purpose
        # The block is supposed to compute it only once
        $this
            ->subject
            ->getMaximumInstallment($product);
        $this
            ->subject
            ->getMaximumInstallment($product);
    }



    public function testGetMaximumInstallmentAmountGetsTheRightValueFromMaximumInstallments()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFinalPrice', 'getId'])
            ->getMock();

        # Can't use the class subject, because we need to mock a tested method getMaximumInstallment
        $subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($this->getConstructorArguments())
            ->setMethods(['getMaximumInstallment', 'getProduct'])
            ->getMock();
        $subject
            ->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $dataObject = $this->objectManager->getObject('\Magento\Framework\DataObject');
        $dataObject->setMaximumInstallmentsAmount(5.10);
        $subject
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->will($this->returnValue($dataObject));

        $this->assertEquals($subject->getMaximumInstallmentAmount(), 5.10);
    }


    public function testGetMaximumInstallmentAmountGetsItFromParentBlockWhenItIsNotDefined()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        # Can't use the class subject, because we need to mock a tested method getMaximumInstallment
        $subject = $this
            ->getMockBuilder($this->className)
            ->disableOriginalConstructor()
            ->setMethods(['getMaximumInstallment', 'getProduct'])
            ->getMock();

        $subject
            ->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $dataObject = $this
            ->getMockBuilder('\Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->setMethods(['getMaximumInstallmentsAmount'])
            ->getMock();

        $dataObject
            ->expects($this->exactly(2))
            ->method('getMaximumInstallmentsAmount')
            ->willReturn(5.10);

        $subject
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->with($product)
            ->will($this->returnValue($dataObject));

        $this->assertEquals($subject->getMaximumInstallmentAmount(), $dataObject->getMaximumInstallmentsAmount());
    }

    public function testGetMaximumInstallmentQuantityGetsTheRightValueFromMaximumInstallments()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFinalPrice', 'getId'])
            ->getMock();

        # Can't use the class subject, because we need to mock a tested method getMaximumInstallment
        $subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($this->getConstructorArguments())
            ->setMethods(['getMaximumInstallment', 'getProduct'])
            ->getMock();
        $subject
            ->expects($this->atLeastOnce())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $dataObject = $this->objectManager->getObject('\Magento\Framework\DataObject');
        $dataObject->setMaximumInstallmentsQuantity(4);
        $subject
            ->expects($this->once())
            ->method('getMaximumInstallment')
            ->will($this->returnValue($dataObject));

        $this->assertEquals($subject->getMaximumInstallmentQuantity(), 4);
    }
}