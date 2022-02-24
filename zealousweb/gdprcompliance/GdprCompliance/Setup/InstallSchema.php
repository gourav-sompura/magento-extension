<?php

namespace Zealousweb\GdprCompliance\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $tableName = $installer->getTable('customer_delete_token');
            if ($installer->getConnection()->isTableExists($tableName) != true) {
                $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_TEXT,
                    55,
                    ['nullable' => false],
                    'Customer ID'
                )
                ->addColumn(
                    'token',
                    Table::TYPE_TEXT,
                    250,
                    ['nullable' => false],
                    'Token'
                )
                ->addColumn(
                    'exp_date',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'Exp. Date'
                )

                ->addIndex(
                    $installer->getIdxName('customer_delete_token', ['customer_id']),
                    ['customer_id']
                );
                $installer->getConnection()->createTable($table);
            }
        }

        $installer->endSetup();
    }
}
