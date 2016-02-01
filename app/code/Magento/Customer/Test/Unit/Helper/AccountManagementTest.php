<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class AccountManagementTest
 * @package Magento\Customer\Test\Unit\Helper
 */
class AccountManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Magento\Framework\App\Helper\Context
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistryMock;

    /**
     * @var \Magento\Customer\Helper\AccountManagement
     */
    protected $helper;

    protected function setUp()
    {
        $this->contextMock = $this->getMock(
            'Magento\Framework\App\Helper\Context',
            [],
            [],
            '',
            false
        );
        $this->indexerRegistryMock = $this->getMock(
            'Magento\Framework\Indexer\IndexerRegistry',
            [],
            [],
            '',
            false
        );
        $objectManagerHelper = new ObjectManagerHelper($this);

        $this->helper = $objectManagerHelper->getObject(
            'Magento\Customer\Helper\AccountManagement',
            [
                'context' => $this->contextMock,
                'indexerRegistry' => $this->indexerRegistryMock
            ]
        );
    }

    /**
     * @param $lockExpirationDate
     * @param $expectedResult
     * @dataProvider isCustomerLockedDataProvider
     */
    public function testIsCustomerLocked($lockExpirationDate, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->helper->isCustomerLocked($lockExpirationDate));
    }

    /**
     * @return array
     */
    public function isCustomerLockedDataProvider()
    {
        return [
          ['lockExpirationDate' => date("F j, Y", strtotime( '-1 days' )), 'expectedResult' => false],
          ['lockExpirationDate' => date("F j, Y", strtotime( '+1 days' )), 'expectedResult' => true]
        ];
    }

    /**
     * @return void
     */
    public function testReindexCustomer()
    {
        $customerId = 7;
        $indexer = $this->getMock(
            'Magento\Framework\Indexer\IndexerInterface',
            [],
            [],
            '',
            false
        );
        $indexer->expects($this->once())
            ->method('reindexList')
            ->with([$customerId]);

        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Customer\Model\Customer::CUSTOMER_GRID_INDEXER_ID)
            ->willReturn($indexer);

        $this->helper->reindexCustomer($customerId);
    }
}
