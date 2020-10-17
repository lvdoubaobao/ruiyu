<?php


namespace Niubi\AbandonedCart\Setup;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements  \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->addColumn(
            $installer->getTable( 'quote' ),
            'abandoned_send',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'length' => 10,
                'comment' => 'abandoned_send',
                'after' => 'store_id',
                'default' => 0
            ]
        );
        if (!$installer->tableExists('niubi_abandoned_record')){
            $table=$installer->getConnection()->newTable(
                $installer->getTable('niubi_abandoned_record')
            )->addColumn(
                'record_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Form ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable => false'],
                'customer_id'
            )->addColumn(
                'cart_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable => false'],
                'cart_id'
            )->addColumn(
                'hour',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable => false'],
                'hour'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                0,
                [],
                'created_time'
            )->addColumn(
                'is_display',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['default'=>0],
                'is_display'
            )->setComment('niubi_abandoned_record');

            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $installer->getTable('niubi_abandoned_record'),
                $setup->getIdxName(
                  $installer->getTable('niubi_abandoned_record') ,
                   'customer_id' ,
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                'customer_id',
                AdapterInterface::INDEX_TYPE_INDEX
            );
            $installer->getConnection()->addIndex(
                $installer->getTable('niubi_abandoned_record'),
                $setup->getIdxName(
                    $installer->getTable('niubi_abandoned_record') ,
                    'cart_id' ,
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                'cart_id',
                AdapterInterface::INDEX_TYPE_INDEX
            );

        }
    }
}