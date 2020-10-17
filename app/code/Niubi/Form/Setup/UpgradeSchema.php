<?php


namespace Niubi\Form\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
class UpgradeSchema  implements  UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();
        if(version_compare($context->getVersion(), '0.0.3', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable( 'niubi_video_form' ),
                'created_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 30,
                    'comment' => 'created_at',
                    'after' => 'name',
                    'default' => ''
                ]
            );
        }

        $installer->endSetup();
    }
}