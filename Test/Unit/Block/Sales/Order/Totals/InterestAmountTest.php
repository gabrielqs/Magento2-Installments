<?php

namespace Gabrielqs\Installments\Test\Unit\Block\Sales\Order\Totals;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Block\Sales\Order\Totals\InterestAmount as Subject;

/**
 * InterestAmount Totals Block Unit Testcase
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
        $this->className = '\Gabrielqs\Installments\Block\Sales\Order\Totals\InterestAmount';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['_getInterestAmount', 'getParentBlock'])
            ->getMock();

        $this->originalObject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);
        return $arguments;
    }

    public function testInitTotalsWontAddTotalIfThereIsNoInterest()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('_getInterestAmount')
            ->will($this->returnValue(null));

        # This method should never be called, as we won't need to add the total since there is no interest applied
        $this
            ->subject
            ->expects($this->never())
            ->method('getParentBlock');

        $this
            ->subject
            ->initTotals();
    }

    public function testInitTotalsWillAddTotalIfThereIsInterest()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('_getInterestAmount')
            ->will($this->returnValue(5.60));

        # This method should be called, as we will need to add the total since there is interest applied
        $totals = $this->getMockBuilder('\Magento\Sales\Block\Order\Totals')
            ->disableOriginalConstructor()
            ->getMock();
        $this
            ->subject
            ->expects($this->once())
            ->method('getParentBlock')
            ->will($this->returnValue($totals));

        $this
            ->subject
            ->initTotals();
    }
}