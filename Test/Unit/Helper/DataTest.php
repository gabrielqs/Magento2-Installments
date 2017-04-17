<?php

namespace Gabrielqs\Installments\Test\Unit\Helper;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Gabrielqs\Installments\Helper\Data as Subject;
use \Magento\Framework\App\Config as ScopeConfig;
use \Magento\Store\Model\StoreManager as StoreManager;
use \Magento\Store\Model\ScopeInterface;

/**
 * Installments Data Helper Unit Testcase
 */
class DataTest extends \PHPUnit_Framework_TestCase
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
     * @var ScopeConfig
     */
    protected $scopeConfig = null;

    /**
     * @var StoreManager
     */
    protected $storeManager = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = '\Gabrielqs\Installments\Helper\Data';
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['getConfigData'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->scopeConfig = $this
            ->getMockBuilder(ScopeConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();
        $arguments['scopeConfig'] = $this->scopeConfig;

        $this->storeManager = $this
            ->getMockBuilder(StoreManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['storeManager'] = $this->storeManager;

        return $arguments;
    }

    public function dataProviderGetConfigDataUsesCorrectKeys()
    {
        return[
            ['foo/bar', null, 1, 'installments/foo/bar'],
            ['foo/baz', 1, 0, 'installments/foo/baz'],
        ];
    }

    /**
     * @param $configPath
     * @param $configStore
     * @param $getStoreExpectedInvokes
     * @param $resultingPath
     *
     * @dataProvider dataProviderGetConfigDataUsesCorrectKeys
     */
    public function testGetConfigDataUsesCorrectKeys($configPath, $configStore, $getStoreExpectedInvokes,
        $resultingPath
    ) {
        $this
            ->storeManager
            ->expects($this->exactly($getStoreExpectedInvokes))
            ->method('getStore')
            ->will($this->returnValue($configStore));

        $return = 'result' . $configPath . $configStore;
        $this
            ->scopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with($resultingPath, ScopeInterface::SCOPE_STORE, $configStore)
            ->will($this->returnValue($return));
        $this->assertEquals($return, $this->originalSubject->getConfigData($configPath, $configStore));
    }

    public function dataProviderGetAllInstallmentPaymentMethodCodes()
    {
        return [
            [
                ['VI' => null, 'MC' => null, 'AE' => null],
                ['VI', 'MC', 'AE']
            ],
            [
                ['VI' => null, 'MC' => null],
                ['VI', 'MC']
            ],
            [
                [],
                []
            ]
        ];
    }

    /**
     * @dataProvider dataProviderGetAllInstallmentPaymentMethodCodes
     */
    public function testGetAllInstallmentPaymentMethodCodes($enabledCcTypesFromConfig, $expectedReturn)
    {
        $this
            ->scopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with('installments/payment_methods')
            ->will($this->returnValue($enabledCcTypesFromConfig));

        $this->assertEquals($expectedReturn, $this->originalSubject->getAllInstallmentPaymentMethodCodes());
    }


    public function dataBooleans()
    {
        return[
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider dataBooleans
     */
    public function testIsConsiderInterestFreeInstallmentsOnlyGetsFromCorrectPathInConfig($paramAndResult)
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getConfigData')
            ->with('maximum_installments/interest_free_only')
            ->will($this->returnValue($paramAndResult));

        $this->assertEquals($paramAndResult, $this->subject->isConsiderInterestFreeInstallmentsOnly());
    }

    /**
     * @dataProvider dataBooleans
     */
    public function testIsShowMaximumInstallmentsProductListGetsFromCorrectPathInConfig($paramAndResult)
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getConfigData')
            ->with('maximum_installments/enable_product_list')
            ->will($this->returnValue($paramAndResult));

        $this->assertEquals($paramAndResult, $this->subject->isShowMaximumInstallmentsProductList());
    }

    /**
     * @dataProvider dataBooleans
     */
    public function testIsShowMaximumInstallmentsProductViewGetsFromCorrectPathInConfig($paramAndResult)
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getConfigData')
            ->with('maximum_installments/enable_product_view')
            ->will($this->returnValue($paramAndResult));

        $this->assertEquals($paramAndResult, $this->subject->isShowMaximumInstallmentsProductView());
    }

    /**
     * @dataProvider dataBooleans
     */
    public function testIsShowMaximumInstallmentsCartGetsFromCorrectPathInConfig($paramAndResult)
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getConfigData')
            ->with('maximum_installments/enable_cart')
            ->will($this->returnValue($paramAndResult));

        $this->assertEquals($paramAndResult, $this->subject->isShowMaximumInstallmentsCart());
    }

    public function testGetMaximumInstallmentGetsInterestOnlyInstallmentsFromConfig()
    {
        $subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($this->getConstructorArguments())
            ->setMethods(['getConfigData', '_getMaximumInstallmentReturnDataObject',
                'isConsiderInterestFreeInstallmentsOnly'])
            ->getMock();

        $dataObject = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dataObject->setMaximumInstallmentsQuantity(1);

        $subject
            ->expects($this->once())
            ->method('_getMaximumInstallmentReturnDataObject')
            ->will($this->returnValue($dataObject));

        $subject
            ->expects($this->once())
            ->method('isConsiderInterestFreeInstallmentsOnly');

        $subject->getMaximumInstallment(100, null);
    }

    protected function _getInstallments($method, $amount)
    {
        $return = [];

        if ($amount == 100) {
            switch ($method) {

                case 'method_1':
                    $installmentA = new \stdClass();
                    $installmentA->interestsApplied = false;
                    $installmentA->numberInstallments = 2;
                    $installmentA->installmentValue = 50;
                    $return[] = $installmentA;

                    $installmentB = new \stdClass();
                    $installmentB->interestsApplied = false;
                    $installmentB->numberInstallments = 3;
                    $installmentB->installmentValue = 33.34;
                    $return[] = $installmentB;

                    $installmentC = new \stdClass();
                    $installmentC->interestsApplied = true;
                    $installmentC->numberInstallments = 4;
                    $installmentC->installmentValue = 28;
                    $return[] = $installmentC;

                    $installmentD = new \stdClass();
                    $installmentD->interestsApplied = true;
                    $installmentD->numberInstallments = 5;
                    $installmentD->installmentValue = 23;
                    $return[] = $installmentD;

                    break;

                case 'method_2':
                    $installmentA = new \stdClass();
                    $installmentA->interestsApplied = false;
                    $installmentA->numberInstallments = 2;
                    $installmentA->installmentValue = 50;
                    $return[] = $installmentA;

                    $installmentB = new \stdClass();
                    $installmentB->interestsApplied = false;
                    $installmentB->numberInstallments = 3;
                    $installmentB->installmentValue = 33.34;
                    $return[] = $installmentB;

                    $installmentC = new \stdClass();
                    $installmentC->interestsApplied = false;
                    $installmentC->numberInstallments = 4;
                    $installmentC->installmentValue = 25;
                    $return[] = $installmentC;
                    break;

                default:
                    break;
            }
        } elseif ($amount == 200) {
            switch ($method) {

                case 'method_1':
                    $installmentA = new \stdClass();
                    $installmentA->interestsApplied = false;
                    $installmentA->numberInstallments = 2;
                    $installmentA->installmentValue = 100;
                    $return[] = $installmentA;

                    $installmentB = new \stdClass();
                    $installmentB->interestsApplied = false;
                    $installmentB->numberInstallments = 3;
                    $installmentB->installmentValue = 66.67;
                    $return[] = $installmentB;

                    $installmentC = new \stdClass();
                    $installmentC->interestsApplied = true;
                    $installmentC->numberInstallments = 4;
                    $installmentC->installmentValue = 55;
                    $return[] = $installmentC;

                    break;

                case 'method_2':
                    $installmentA = new \stdClass();
                    $installmentA->interestsApplied = false;
                    $installmentA->numberInstallments = 2;
                    $installmentA->installmentValue = 100;
                    $return[] = $installmentA;

                    $installmentB = new \stdClass();
                    $installmentB->interestsApplied = false;
                    $installmentB->numberInstallments = 3;
                    $installmentB->installmentValue = 66.67;
                    $return[] = $installmentB;

                    $installmentC = new \stdClass();
                    $installmentC->interestsApplied = false;
                    $installmentC->numberInstallments = 4;
                    $installmentC->installmentValue = 25;
                    $return[] = $installmentC;

                    $installmentD = new \stdClass();
                    $installmentD->interestsApplied = false;
                    $installmentD->numberInstallments = 5;
                    $installmentD->installmentValue = 23;
                    $return[] = $installmentD;
                    break;

                default:
                    break;
            }
        }


        return $return;
    }

    public function dataProviderGetMaximumInstallmentCalculation()
    {
        $objectManager = new ObjectManager($this);
        $expectedReturnA = $objectManager
            ->getObject(\Magento\Framework\DataObject::class,
                $objectManager->getConstructArguments(\Magento\Framework\DataObject::class)
            );
        $expectedReturnA
            ->setMaximumInstallmentsQuantity(4)
            ->setMaximumInstallmentsAmount(25);

        $expectedReturnB = $objectManager
            ->getObject(\Magento\Framework\DataObject::class,
                $objectManager->getConstructArguments(\Magento\Framework\DataObject::class)
            );
        $expectedReturnB
            ->setMaximumInstallmentsQuantity(5)
            ->setMaximumInstallmentsAmount(23);
        return
            [
                [100, true, $expectedReturnA],
                [200, false, $expectedReturnB]
            ];
    }

    /**
     * @dataProvider dataProviderGetMaximumInstallmentCalculation
     */
    public function testGetMaximumInstallmentCalculation($amount, $interestFreeOnly, $expectedResult)
    {
        $subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($this->getConstructorArguments())
            ->setMethods(['getConfigData', '_getMaximumInstallmentReturnDataObject',
                'getAllInstallmentPaymentMethodCodes', '_getInstallmentsHelperFromPaymentMethod'])
            ->getMock();

        $installmentReturnObject = $this
            ->objectManager
            ->getObject(\Magento\Framework\DataObject::class,
                $this->objectManager->getConstructArguments(\Magento\Framework\DataObject::class)
            );
        $installmentReturnObject->setMaximumInstallmentsQuantity(1);

        $subject
            ->expects($this->once())
            ->method('_getMaximumInstallmentReturnDataObject')
            ->will($this->returnValue($installmentReturnObject));

        $subject
            ->expects($this->once())
            ->method('getAllInstallmentPaymentMethodCodes')
            ->will($this->returnValue(['method_1', 'method_2']));


        $calculator = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInstallments'])
            ->getMock();
        $calculator
            ->expects($this->exactly(2))
            ->method('getInstallments')
            ->withConsecutive(
                [$amount],
                [$amount]
            )
            ->willReturnOnConsecutiveCalls(
                $this->_getInstallments('method_1', $amount),
                $this->_getInstallments('method_2', $amount)
            );

        $installmentsPaymentHelper = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInstallmentsCalculator'])
            ->getMock();
        $installmentsPaymentHelper
            ->expects($this->exactly(2))
            ->method('getInstallmentsCalculator')
            ->willReturnOnConsecutiveCalls(
                $calculator,
                $calculator
            );

        $subject
            ->expects($this->exactly(2))
            ->method('_getInstallmentsHelperFromPaymentMethod')
            ->withConsecutive(['method_1'], ['method_2'])
            ->willReturnOnConsecutiveCalls(
                $installmentsPaymentHelper,
                $installmentsPaymentHelper
            );

        $this->assertEquals($expectedResult, $subject->getMaximumInstallment($amount, $interestFreeOnly));
    }
}