<?php

namespace Gabrielqs\Installments\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\DataObject;
use \Magento\Framework\DataObjectFactory;
use \Gabrielqs\Installments\Model\Calculator as Subject;

/**
 * Calculator Unit Testcase
 */
class CalculatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * Data Object Factory
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory = null;

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
        $this->className = '\Gabrielqs\Installments\Model\Calculator';
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

        $this->_dataObjectFactory = $this
            ->getMockBuilder(DataObjectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $arguments['dataObjectFactory'] = $this->_dataObjectFactory;

        return $arguments;
    }

    protected function _initCalculator(
        $interestRate,
        $maximumInstallmentQuantity,
        $minimumAmountNoInterest,
        $minimumInstallmentAmount,
        $paymentAmount
    ) {
        $this->subject
            ->setInterestRate($interestRate)
            ->setMaximumInstallmentQuantity($maximumInstallmentQuantity)
            ->setMinimumAmountNoInterest($minimumAmountNoInterest)
            ->setMinimumInstallmentAmount($minimumInstallmentAmount)
            ->setPaymentAmount($paymentAmount);
    }

    public function testGetInstallmentConfigReturnsDataObjectWithRightFormat()
    {
        $minimumAmountNoInterest = [
            2 => 200,
            3 => 300,
            4 => 400,
            5 => 500
        ];

        $this->_initCalculator(1.0499,
            10,
            $minimumAmountNoInterest,
            20,
            100);

        $this
            ->_dataObjectFactory
            ->expects($this->exactly(11))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallmentConfig();

        $this->assertInstanceOf('\Magento\Framework\DataObject', $return);

        $this->assertEquals(10, $return->maximumInstallmentQty);
        $this->assertEquals(20, $return->minimumInstallmentAmount);
        $this->assertEquals(1.0499, $return->interestRate);
        $this->assertInternalType('array', $return->installments);
        $this->assertEquals(10, count($return->installments));

        $this->assertEquals(1, $return->installments[1]->interestRate);
        $this->assertEquals(1, $return->installments[1]->numberInstallments);
        $this->assertNull($return->installments[1]->minimumAmountNoInterest);

        $this->assertEquals(1.0499, $return->installments[2]->interestRate);
        $this->assertEquals(2, $return->installments[2]->numberInstallments);
        $this->assertEquals(200, $return->installments[2]->minimumAmountNoInterest);

        $this->assertEquals(1.10229001, $return->installments[3]->interestRate);
        $this->assertEquals(3, $return->installments[3]->numberInstallments);
        $this->assertEquals(300, $return->installments[3]->minimumAmountNoInterest);

        $this->assertEquals(1.157294281499, $return->installments[4]->interestRate);
        $this->assertEquals(4, $return->installments[4]->numberInstallments);
        $this->assertEquals(400, $return->installments[4]->minimumAmountNoInterest);

        $this->assertEquals(1.2150432661458, $return->installments[5]->interestRate);
        $this->assertEquals(5, $return->installments[5]->numberInstallments);
        $this->assertEquals(500, $return->installments[5]->minimumAmountNoInterest);

        $this->assertEquals(1.2756739251265, $return->installments[6]->interestRate);
        $this->assertEquals(6, $return->installments[6]->numberInstallments);
        $this->assertNull($return->installments[6]->minimumAmountNoInterest);

        $this->assertEquals(1.3393300539903, $return->installments[7]->interestRate);
        $this->assertEquals(7, $return->installments[7]->numberInstallments);
        $this->assertNull($return->installments[7]->minimumAmountNoInterest);

        $this->assertEquals(1.4061626236844, $return->installments[8]->interestRate);
        $this->assertEquals(8, $return->installments[8]->numberInstallments);
        $this->assertNull($return->installments[8]->minimumAmountNoInterest);

        $this->assertEquals(1.4763301386063, $return->installments[9]->interestRate);
        $this->assertEquals(9, $return->installments[9]->numberInstallments);
        $this->assertNull($return->installments[9]->minimumAmountNoInterest);

        $this->assertEquals(1.5499990125227, $return->installments[10]->interestRate);
        $this->assertEquals(10, $return->installments[10]->numberInstallments);
        $this->assertNull($return->installments[10]->minimumAmountNoInterest);
    }

    public function testGetInstallmentConfigReturnsOneInstallmentOnlyWhenMaximumInstallmentQuantityEqualsOne()
    {
        $this->_initCalculator(1, 1, null, null, null);

        $this
            ->_dataObjectFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallmentConfig();

        $this->assertEquals(1, $return->maximumInstallmentQty);
        $this->assertEquals(0, $return->minimumInstallmentAmount);
        $this->assertEquals(1, $return->interestRate);
        $this->assertInternalType('array', $return->installments);
        $this->assertEquals(1, count($return->installments));

        $this->assertEquals(1, $return->installments[1]->interestRate);
        $this->assertEquals(1, $return->installments[1]->numberInstallments);
        $this->assertNull($return->installments[1]->minimumAmountNoInterest);
    }

    public function testGetInstallmentConfigReturnsInterestFreeInstallmentsCorrectly()
    {
        $minimumAmountNoInterest = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];

        $this->_initCalculator(1.0199,
            12,
            $minimumAmountNoInterest,
            5,
            100);

        $this
            ->_dataObjectFactory
            ->expects($this->exactly(13))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallmentConfig();

        $this->assertEquals(12, $return->maximumInstallmentQty);
        $this->assertEquals(5, $return->minimumInstallmentAmount);
        $this->assertEquals(1.0199, $return->interestRate);
        $this->assertInternalType('array', $return->installments);
        $this->assertEquals(12, count($return->installments));

        $this->assertEquals(1, $return->installments[1]->interestRate);
        $this->assertEquals(1, $return->installments[1]->numberInstallments);
        $this->assertNull($return->installments[1]->minimumAmountNoInterest);

        $this->assertEquals(1.0199, $return->installments[2]->interestRate);
        $this->assertEquals(2, $return->installments[2]->numberInstallments);
        $this->assertEquals(0, $return->installments[2]->minimumAmountNoInterest);

        $this->assertEquals(1.0401960100000001, $return->installments[3]->interestRate);
        $this->assertEquals(3, $return->installments[3]->numberInstallments);
        $this->assertEquals(0, $return->installments[3]->minimumAmountNoInterest);

        $this->assertEquals(1.0608959105990001, $return->installments[4]->interestRate);
        $this->assertEquals(4, $return->installments[4]->numberInstallments);
        $this->assertEquals(0, $return->installments[4]->minimumAmountNoInterest);

        $this->assertEquals(1.0820077392199203, $return->installments[5]->interestRate);
        $this->assertEquals(5, $return->installments[5]->numberInstallments);
        $this->assertEquals(0, $return->installments[5]->minimumAmountNoInterest);

        $this->assertEquals(1.1035396932303967, $return->installments[6]->interestRate);
        $this->assertEquals(6, $return->installments[6]->numberInstallments);
        $this->assertNull($return->installments[6]->minimumAmountNoInterest);

        $this->assertEquals(1.1255001331256815, $return->installments[7]->interestRate);
        $this->assertEquals(7, $return->installments[7]->numberInstallments);
        $this->assertNull($return->installments[7]->minimumAmountNoInterest);

        $this->assertEquals(1.1478975857748828, $return->installments[8]->interestRate);
        $this->assertEquals(8, $return->installments[8]->numberInstallments);
        $this->assertNull($return->installments[8]->minimumAmountNoInterest);

        $this->assertEquals(1.1707407477318028, $return->installments[9]->interestRate);
        $this->assertEquals(9, $return->installments[9]->numberInstallments);
        $this->assertNull($return->installments[9]->minimumAmountNoInterest);

        $this->assertEquals(1.1940384886116657, $return->installments[10]->interestRate);
        $this->assertEquals(10, $return->installments[10]->numberInstallments);
        $this->assertNull($return->installments[10]->minimumAmountNoInterest);

        $this->assertEquals(1.2177998545350379, $return->installments[11]->interestRate);
        $this->assertEquals(11, $return->installments[11]->numberInstallments);
        $this->assertNull($return->installments[11]->minimumAmountNoInterest);

        $this->assertEquals(1.2420340716402853, $return->installments[12]->interestRate);
        $this->assertEquals(12, $return->installments[12]->numberInstallments);
        $this->assertNull($return->installments[12]->minimumAmountNoInterest);
    }

    public function testGetInstallmentsReturnsDataObjectWithRightFormat()
    {
        $minimumAmountNoInterest = [
            2 => 200,
            3 => 300,
            4 => 400,
            5 => 500
        ];

        $this->_initCalculator(1.0499,
            10,
            $minimumAmountNoInterest,
            20,
            100);

        $this
            ->_dataObjectFactory
            ->expects($this->exactly(10))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallments(525.30);

        $this->assertInternalType('array', $return);
        $this->assertEquals(10, count($return));

        $this->assertEquals(525.30, $return[1]->installmentValue);
        $this->assertEquals(1, $return[1]->numberInstallments);
        $this->assertFalse($return[1]->interestsApplied);

        $this->assertEquals(262.65, $return[2]->installmentValue);
        $this->assertEquals(2, $return[2]->numberInstallments);
        $this->assertFalse($return[2]->interestsApplied);

        $this->assertEquals(175.10, $return[3]->installmentValue);
        $this->assertEquals(3, $return[3]->numberInstallments);
        $this->assertFalse($return[3]->interestsApplied);

        $this->assertEquals(131.32499999999999, $return[4]->installmentValue);
        $this->assertEquals(4, $return[4]->numberInstallments);
        $this->assertFalse($return[4]->interestsApplied);

        $this->assertEquals(105.05999999999999, $return[5]->installmentValue);
        $this->assertEquals(5, $return[5]->numberInstallments);
        $this->assertFalse($return[5]->interestsApplied);

        $this->assertEquals(111.68525214482297, $return[6]->installmentValue);
        $this->assertEquals(6, $return[6]->numberInstallments);
        $this->assertTrue($return[6]->interestsApplied);

        $this->assertEquals(100.50715390872824, $return[7]->installmentValue);
        $this->assertEquals(7, $return[7]->numberInstallments);
        $this->assertTrue($return[7]->interestsApplied);

        $this->assertEquals(92.332153277677065, $return[8]->installmentValue);
        $this->assertEquals(8, $return[8]->numberInstallments);
        $this->assertTrue($return[8]->interestsApplied);

        $this->assertEquals(86.168469089985024, $return[9]->installmentValue);
        $this->assertEquals(9, $return[9]->numberInstallments);
        $this->assertTrue($return[9]->interestsApplied);

        $this->assertEquals(81.421448127817754, $return[10]->installmentValue);
        $this->assertEquals(10, $return[10]->numberInstallments);
        $this->assertTrue($return[10]->interestsApplied);
    }


    public function testGetInstallmentsReturnsOneInstallmentOnlyWhenMaximumInstallmentQuantityEqualsOne()
    {
        $this->_initCalculator(1, 1, null, null, null);

        $this
            ->_dataObjectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallments(60.27);

        $this->assertInternalType('array', $return);
        $this->assertEquals(1, count($return));

        $this->assertEquals(60.27, $return[1]->installmentValue);
        $this->assertEquals(1, $return[1]->numberInstallments);
        $this->assertFalse($return[1]->interestsApplied);

    }

    public function testGetInstallmentsReturnsDataObjectWithRightFormatAndAppliesInterestToAllInstallmentOptions()
    {
        $minimumAmountNoInterest = [
            2 => 2000,
            3 => 3000,
            4 => 4000,
            5 => 5000
        ];

        $this->_initCalculator(1.0239,
            12,
            $minimumAmountNoInterest,
            5,
            100);

        $this
            ->_dataObjectFactory
            ->expects($this->exactly(12))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallments(85.92);

        $this->assertInternalType('array', $return);
        $this->assertEquals(12, count($return));

        $this->assertEquals(85.92, $return[1]->installmentValue);
        $this->assertEquals(1, $return[1]->numberInstallments);
        $this->assertFalse($return[1]->interestsApplied);

        $this->assertEquals(43.986744000000002, $return[2]->installmentValue);
        $this->assertEquals(2, $return[2]->numberInstallments);
        $this->assertTrue($return[2]->interestsApplied);

        $this->assertEquals(30.025351454399999, $return[3]->installmentValue);
        $this->assertEquals(3, $return[3]->numberInstallments);
        $this->assertTrue($return[3]->interestsApplied);

        $this->assertEquals(23.057218015620123, $return[4]->installmentValue);
        $this->assertEquals(4, $return[4]->numberInstallments);
        $this->assertTrue($return[4]->interestsApplied);

        $this->assertEquals(18.886628420954757, $return[5]->installmentValue);
        $this->assertEquals(5, $return[5]->numberInstallments);
        $this->assertTrue($return[5]->interestsApplied);

        $this->assertEquals(16.115015700179644, $return[6]->installmentValue);
        $this->assertEquals(6, $return[6]->numberInstallments);
        $this->assertTrue($return[6]->interestsApplied);

        $this->assertEquals(14.14299820749766, $return[7]->installmentValue);
        $this->assertEquals(7, $return[7]->numberInstallments);
        $this->assertTrue($return[7]->interestsApplied);

        $this->assertEquals(12.670888881574751, $return[8]->installmentValue);
        $this->assertEquals(8, $return[8]->numberInstallments);
        $this->assertTrue($return[8]->interestsApplied);

        $this->assertEquals(11.532198334083898, $return[9]->installmentValue);
        $this->assertEquals(9, $return[9]->numberInstallments);
        $this->assertTrue($return[9]->interestsApplied);

        $this->assertEquals(10.627036086841654, $return[10]->installmentValue);
        $this->assertEquals(10, $return[10]->numberInstallments);
        $this->assertTrue($return[10]->interestsApplied);

        $this->assertEquals(9.8918384084701554, $return[11]->installmentValue);
        $this->assertEquals(11, $return[11]->numberInstallments);
        $this->assertTrue($return[11]->interestsApplied);

        $this->assertEquals(9.2842322342298758, $return[12]->installmentValue);
        $this->assertEquals(12, $return[12]->numberInstallments);
        $this->assertTrue($return[12]->interestsApplied);
    }


    public function testGetInstallmentsReturnsDataObjectWithRightFormatAndSkipsSmallInstallmentsDueToConfiguration()
    {
        $minimumAmountNoInterest = [
            2 => 100,
            3 => 200,
        ];

        $this->_initCalculator(1.0439,
            12,
            $minimumAmountNoInterest,
            100,
            100);

        $this
            ->_dataObjectFactory
            ->expects($this->exactly(5))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInstallments(428.89);

        $this->assertInternalType('array', $return);
        $this->assertEquals(5, count($return));

        $this->assertEquals(428.89, $return[1]->installmentValue);
        $this->assertEquals(1, $return[1]->numberInstallments);
        $this->assertFalse($return[1]->interestsApplied);

        $this->assertEquals(214.445, $return[2]->installmentValue);
        $this->assertEquals(2, $return[2]->numberInstallments);
        $this->assertFalse($return[2]->interestsApplied);

        $this->assertEquals(142.96333333333334, $return[3]->installmentValue);
        $this->assertEquals(3, $return[3]->numberInstallments);
        $this->assertFalse($return[3]->interestsApplied);

        $this->assertEquals(121.97269558071348, $return[4]->installmentValue);
        $this->assertEquals(4, $return[4]->numberInstallments);
        $this->assertTrue($return[4]->interestsApplied);
    }

    public function dataProvidertestGetInterestAmountReturnsExpectedValues()
    {

        $minimumAmountNoInterestOne = [
            2 => 100,
            3 => 200,
        ];

        $minimumAmountNoInterestTwo = [];

        $minimumAmountNoInterestThree = [
            2 => 50,
            3 => 100,
            4 => 150,
            5 => 200,
            6 => 250,
        ];

        $minimumAmountNoInterestFour = [
            2 => 600,
            3 => 800,
        ];

        return[
            [100, 1, $minimumAmountNoInterestOne, 1.0199, 8, 5, 0],
            [50, 2, $minimumAmountNoInterestOne, 1.0199, 8, 5, 0.99500000000000455],
            [240, 12, $minimumAmountNoInterestTwo, 1.0299, 12, 5, 91.86150722962617],
            [10, 1, $minimumAmountNoInterestThree, 1.0499, 8, 5, 0],
            [1200, 4, $minimumAmountNoInterestFour, 1.0150, 8, 5, 54.814049999999497],
            [800, 3, $minimumAmountNoInterestFour, 1, 8, 5, 0]
        ];
    }

    /**
     * @param $paymentAmount
     * @param $installmentQuantity
     * @param $minimumAmountNoInterest
     * @param $interestRate
     * @param $maximumInstallmentQuantity
     * @param $minimumInstallmentAmount
     * @param $expectedValue
     * @dataProvider dataProvidertestGetInterestAmountReturnsExpectedValues
     */
    public function testGetInterestAmountReturnsExpectedValues(
        $paymentAmount,
        $installmentQuantity,
        $minimumAmountNoInterest,
        $interestRate,
        $maximumInstallmentQuantity,
        $minimumInstallmentAmount,
        $expectedValue
    ) {
        $this->_initCalculator($interestRate,
            $maximumInstallmentQuantity,
            $minimumAmountNoInterest,
            $minimumInstallmentAmount,
            $paymentAmount);

        $this
            ->_dataObjectFactory
            ->expects($this->any())
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject()),
                $this->returnValue(new DataObject())
            );

        $return = $this->subject->getInterestAmount($paymentAmount, $installmentQuantity);
        $this->assertEquals($expectedValue, $return);
    }

}