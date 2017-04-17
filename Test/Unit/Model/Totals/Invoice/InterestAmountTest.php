<?php

namespace Gabrielqs\Installments\Test\Unit\Model\Totals\Invoice;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Model\Totals\Invoice\InterestAmount as Subject;
use \Magento\Sales\Model\Order\Invoice;


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
        $this->className = '\Gabrielqs\Installments\Model\Totals\Invoice\InterestAmount';
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
        $invoiceMock = $this->getMockBuilder(Invoice::class)
            ->setConstructorArgs($this->objectManager->getConstructArguments(Invoice::class))
            ->setMethods(null)
            ->getMock();
        $invoiceMock
            ->setBaseGrandTotal(100)
            ->setGrandTotal(200)
            ->setBaseGabrielqsInstallmentsInterestAmount(1)
            ->setGabrielqsInstallmentsInterestAmount(2);

        $this->subject->collect($invoiceMock);

        $this->assertEquals(1, $invoiceMock->getBaseGabrielqsInstallmentsInterestAmount());
        $this->assertEquals(2, $invoiceMock->getGabrielqsInstallmentsInterestAmount());
        $this->assertEquals(101, $invoiceMock->getBaseGrandTotal());
        $this->assertEquals(202, $invoiceMock->getGrandTotal());
    }
}
