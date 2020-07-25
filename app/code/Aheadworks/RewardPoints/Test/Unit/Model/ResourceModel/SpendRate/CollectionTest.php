<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\SpendRate;

use Aheadworks\RewardPoints\Model\ResourceModel\SpendRate\Collection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\SpendRate\CollectionTest
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    private $object;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $resource = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock->expects($this->atLeastOnce())
            ->method('select')
            ->willReturn($selectMock);
        $resource->expects($this->once())
            ->method('getConnection')
            ->willReturn($connectionMock);

        $data = [
            'resource' => $resource,
        ];

        $this->object = $objectManager->getObject(Collection::class, $data);
    }

    /**
     * Test toConfigDataArray method
     */
    public function testToConfigDataArray()
    {
        $expectedValues = [
            [
                'website_id' => 5,
                'customer_group_id' => 11,
                'points' => 1000,
                'orig_data' => null,
            ],
            [
                'website_id' => 1,
                'customer_group_id' => 10,
                'points' => 500,
                'orig_data' => null,
            ]

        ];

        foreach ($expectedValues as $value) {
            $this->object->addItem(new \Magento\Framework\DataObject($value));
        }

        $this->assertEquals($expectedValues, $this->object->toConfigDataArray());
    }
}
