<?php

namespace Gabrielqs\Installments\Test\Unit\Model\Config\Source;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Model\Config\Source\PaymentMethods as Subject;
use \Gabrielqs\Installments\Helper\Data as InstallmentsHelper;



/**
 * Unit Testcase
 */
class PaymentMethodsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var InstallmentsHelper
     */
    protected $installmentsHelper = null;

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
        $this->className = 'Gabrielqs\Installments\Model\Config\Source\PaymentMethods';
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

        $this->installmentsHelper = $this
            ->getMockBuilder(InstallmentsHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllInstallmentPaymentMethodCodes'])
            ->getMock();
        $arguments['_installmentsHelper'] = $this->installmentsHelper;

        return $arguments;
    }

    public function testToOptionArray()
    {
        $this
            ->installmentsHelper
            ->expects($this->once())
            ->method('getAllInstallmentPaymentMethodCodes')
            ->will($this->returnValue([
                'one_method',
                'another_method',
                'foobaz'
            ]));


        $expectedReturn = [
            [
                'value' => 'one_method',
                'label' => 'One method'
            ],
            [
                'value' => 'another_method',
                'label' => 'Another method'
            ],
            [
                'value' => 'foobaz',
                'label' => 'Foobaz'
            ],
        ];

        $this->assertEquals($expectedReturn, $this->originalSubject->toOptionArray());
    }
}