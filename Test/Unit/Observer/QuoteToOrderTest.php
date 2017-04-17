<?php

namespace Gabrielqs\Installments\Test\Unit\Observer;

use Gabrielqs\Installments\Helper\Data;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Observer\QuoteToOrder as Subject;
use \Magento\Framework\DataObject;
use \Magento\Framework\Event\Observer;

/**
 * Unit Testcase
 */
class QuoteToOrderTest extends \PHPUnit_Framework_TestCase
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
        $this->className = '\Gabrielqs\Installments\Observer\QuoteToOrder';
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

    public function testExecute()
    {
        $constructorArguments = $this->objectManager->getConstructArguments(Observer::class);
        $observerMock = $this->objectManager->getObject(Observer::class, $constructorArguments);

        $constructorArguments = $this->objectManager->getConstructArguments(DataObject::class);
        $orderMock = $this
            ->getMockBuilder(DataObject::class)
            ->setMethods(['setGabrielqsInstallmentsQty', 'setGabrielqsInstallmentsInterestRate',
                'setGabrielqsInstallmentsInterestAmount', 'setBaseGabrielqsInstallmentsInterestAmount'])
            ->setConstructorArgs($constructorArguments)
            ->getMock();
        $orderMock
            ->expects($this->once())
            ->method('setGabrielqsInstallmentsQty')
            ->with(3)
            ->will($this->returnValue($orderMock));
        $orderMock
            ->expects($this->once())
            ->method('setGabrielqsInstallmentsInterestRate')
            ->with(1.0432)
            ->will($this->returnValue($orderMock));
        $orderMock
            ->expects($this->once())
            ->method('setGabrielqsInstallmentsInterestAmount')
            ->with(4.32)
            ->will($this->returnValue($orderMock));
        $orderMock
            ->expects($this->once())
            ->method('setBaseGabrielqsInstallmentsInterestAmount')
            ->with(8.64)
            ->will($this->returnValue($orderMock));



        $quoteMock = $this->objectManager->getObject(DataObject::class, $constructorArguments);
        $quoteMock
            ->setGabrielqsInstallmentsQty(3)
            ->setGabrielqsInstallmentsInterestRate(1.0432)
            ->setGabrielqsInstallmentsInterestAmount(4.32)
            ->setBaseGabrielqsInstallmentsInterestAmount(8.64);

        $observerMock->setOrder($orderMock)->setQuote($quoteMock);

        $this->originalSubject->execute($observerMock);
    }
}