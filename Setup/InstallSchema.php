<?php

namespace Gabrielqs\Installments\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\DB\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        # Quote Fields
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'gabrielqs_installments_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Quantity'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'base_gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount - Base'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'gabrielqs_installments_interest_rate',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Rate'
            ]
        );


        # Order Fields
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'gabrielqs_installments_qty',
            [
                'type' => Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Quantity'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'base_gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount - Base'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'gabrielqs_installments_interest_rate',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Rate'
            ]
        );

        # Invoice Fields
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_invoice'),
            'gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_invoice'),
            'base_gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount - Base'
            ]
        );

        # Shipment Fields
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_shipment'),
            'gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_shipment'),
            'base_gabrielqs_installments_interest_amount',
            [
                'type' => Table::TYPE_FLOAT,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Gabrielqs Installments Interest Amount - Base'
            ]
        );

        $setup->endSetup();
    }
}