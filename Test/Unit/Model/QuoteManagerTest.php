<?php

namespace Gabrielqs\Installments\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Model\QuoteManager as Subject;
use \Gabrielqs\Installments\Model\Calculator;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Quote\Model\Quote;


/**
 * Unit Testcase
 */
class QuoteManagerTest extends \PHPUnit_Framework_TestCase
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
        $this->className = '\Gabrielqs\Installments\Model\QuoteManager';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['_getQuote', '_getPaymentAmount', '_getBasePaymentAmount'])
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

    public function testSetInstallmentDataBeforeAuthorizationNoCalculatorSetShouldThrowException()
    {
        $this->setExpectedException(LocalizedException::class);
        $this->subject->setInstallmentDataBeforeAuthorization(2);
    }

    public function testSetInstallmentDataBeforeAuthorizationShouldUseCalculatorAndSetInfoToQuote()
    {
        $calculator = $this->getMockBuilder(Calculator::class)
            ->setMethods(['getInterestRateForInstallment', 'getInterestAmount'])
            ->disableOriginalConstructor()
            ->getMock();

        $calculator
            ->expects($this->any())
            ->method('getInterestRateForInstallment')
            ->with(2)
            ->will($this->returnValue(1.0249));

        $calculator
            ->expects($this->exactly(2))
            ->method('getInterestAmount')
            ->withConsecutive(
                [100.10, 2],
                [200.20, 2]
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(5.62),
                $this->returnValue(12.24)
            );

        $quote = $this->getMockBuilder(Quote::class)
            ->setMethods(['collectTotals', 'setGabrielqsInstallmentsQty', 'setGabrielqsInstallmentsInterestRate',
                'setGabrielqsInstallmentsInterestAmount', 'setBaseGabrielqsInstallmentsInterestAmount',
                'setTotalsCollectedFlag'])
            ->disableOriginalConstructor()
            ->getMock();
        $quote
            ->expects($this->once())
            ->method('setGabrielqsInstallmentsQty')
            ->with(2)
            ->will($this->returnValue($quote));
        $quote
            ->expects($this->once())
            ->method('setGabrielqsInstallmentsInterestRate')
            ->with(1.0249)
            ->will($this->returnValue($quote));
        $quote
            ->expects($this->once())
            ->method('setGabrielqsInstallmentsInterestAmount')
            ->with(5.62)
            ->will($this->returnValue($quote));
        $quote
            ->expects($this->once())
            ->method('setBaseGabrielqsInstallmentsInterestAmount')
            ->with(12.24)
            ->will($this->returnValue($quote));
        $quote
            ->expects($this->once())
            ->method('setTotalsCollectedFlag')
            ->with(false)
            ->will($this->returnValue($quote));
        $quote
            ->expects($this->once())
            ->method('collectTotals');


        $this
            ->subject
            ->expects($this->any())
            ->method('_getQuote')
            ->will($this->returnValue($quote));

        $this
            ->subject
            ->expects($this->any())
            ->method('_getPaymentAmount')
            ->will($this->returnValue(100.10));

        $this
            ->subject
            ->expects($this->any())
            ->method('_getBasePaymentAmount')
            ->will($this->returnValue(200.20));


        $this->subject->setCalculator($calculator);

        $this->subject->setInstallmentDataBeforeAuthorization(2);
    }
}