<?php

namespace Gabrielqs\Installments\Test\Unit\Model\Totals\Quote;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Model\Totals\Quote\InterestAmount as Subject;
use \Magento\Quote\Model\Quote;
use \Magento\Quote\Api\Data\ShippingAssignmentInterface;
use \Magento\Quote\Model\Quote\Address\Total;
use \Magento\Quote\Model\Quote\Address;
use \Magento\Quote\Api\Data\ShippingInterface;

/**
 * InterestAmountTest Credit Memo Totals Unit Testcase
 */
class InterestAmountTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = '\Gabrielqs\Installments\Model\Totals\Quote\InterestAmount';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(null)
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);
        return $arguments;
    }

    public function testCollect()
    {
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setConstructorArgs($this->objectManager->getConstructArguments(Quote::class))
            ->setMethods(null)
            ->getMock();
        $quoteMock
            ->setBaseGrandTotal(100)
            ->setGrandTotal(200)
            ->setBaseGabrielqsInstallmentsInterestAmount(1)
            ->setGabrielqsInstallmentsInterestAmount(2);

        $addressMock = $this->getMock(Address::class, [], $this->objectManager->getConstructArguments(Address::class));
        $shippingMock = $this->getMock(ShippingInterface::class);
        $shippingMock
            ->expects($this->once())
            ->method('getAddress')
            ->will($this->returnValue($addressMock));
        $shippingAssignment = $this->getMock(ShippingAssignmentInterface::class);
        $shippingAssignment
            ->expects($this->once())
            ->method('getShipping')
            ->will($this->returnValue($shippingMock));
        $totals = $this->getMockBuilder(Total::class)
            ->setMethods(null)
            ->getMock();

        $totals
            ->setBaseGrandTotal(100)
            ->setGrandTotal(200);


        $this->subject->collect($quoteMock, $shippingAssignment, $totals);

        $this->assertEquals(1, $totals->getBaseTotalAmount('gabrielqs_installments_interest_amount'));
        $this->assertEquals(2, $totals->getTotalAmount('gabrielqs_installments_interest_amount'));
        $this->assertEquals(101, $totals->getBaseGrandTotal());
        $this->assertEquals(202, $totals->getGrandTotal());
    }

    public function testLabelIsCorrect()
    {
        $this->assertEquals('Interest', $this->subject->getLabel());
    }
}
