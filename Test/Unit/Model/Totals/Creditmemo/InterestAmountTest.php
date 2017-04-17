<?php

namespace Gabrielqs\Installments\Test\Unit\Model\Totals\Creditmemo;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Model\Totals\Creditmemo\InterestAmount as Subject;
use \Magento\Sales\Model\Order\Creditmemo;
use \Magento\Sales\Model\Order;


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
        $this->className = '\Gabrielqs\Installments\Model\Totals\Creditmemo\InterestAmount';
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
        $creditMemoMock = $this->getMockBuilder(Creditmemo::class)
            ->setConstructorArgs($this->objectManager->getConstructArguments(Creditmemo::class))
            ->setMethods(null)
            ->getMock();
        $creditMemoMock
            ->setBaseGrandTotal(100)
            ->setGrandTotal(200);


        $orderMock = $this->getMockBuilder(Order::class)
            ->setConstructorArgs($this->objectManager->getConstructArguments(Order::class))
            ->setMethods(null)
            ->getMock();
        $orderMock
            ->setBaseGabrielqsInstallmentsInterestAmount(1)
            ->setGabrielqsInstallmentsInterestAmount(2);

        $creditMemoMock->setOrder($orderMock);

        $this->subject->collect($creditMemoMock);

        $this->assertEquals(1, $creditMemoMock->getBaseGabrielqsInstallmentsInterestAmount());
        $this->assertEquals(2, $creditMemoMock->getGabrielqsInstallmentsInterestAmount());
        $this->assertEquals(101, $creditMemoMock->getBaseGrandTotal());
        $this->assertEquals(202, $creditMemoMock->getGrandTotal());
    }
}
