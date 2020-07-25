<?php
namespace Mazentop\AdvancedReview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable($installer->getTable('review_advanced'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'review_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Review id'
            )
            ->addColumn(
                'advanced_img',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => true],
                'Review Img'
            )
            ->addColumn(
                'advanced_video',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => true],
                'Review Video'
            )
            ->addIndex(
                $installer->getIdxName('review_advanced', ['review_id']),
                ['review_id']
            )
            ->setComment('Advanced Review');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
