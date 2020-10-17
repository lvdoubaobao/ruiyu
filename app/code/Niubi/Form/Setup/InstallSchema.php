<?php


namespace Niubi\Form\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements  \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('niubi_video_form')){
            $table=$installer->getConnection()->newTable(
                $installer->getTable('niubi_video_form')
            )->addColumn(
                'form_id',
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
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Name'
            )->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Email'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    20,
                ['nullable => false'],
                'TYPE'
            )->addColumn(
                'link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable => false'],
                'link'
            )->addColumn(
                   'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Content'
            )->setComment('Video Form Table');
            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $installer->getTable('niubi_video_form'),
                $setup->getIdxName(
                    $installer->getTable('niubi_video_form'),
                    ['name', 'email', 'type','link', 'content'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name', 'email', 'type','link', 'content'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    }
}