<?php
/**
 * Faonni
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future.
 * 
 * @package     Faonni_LifeTimeReminder
 * @copyright   Copyright (c) 2015 Karliuka Vitalii(karliuka.vitalii@gmail.com) 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
/** @var $installer Faonni_LifeTimeReminder_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Create table 'faonni_lifetimereminder/schedule'
 */
$table = $connection
    ->newTable($installer->getTable('faonni_lifetimereminder/schedule'))
	
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
		'nullable'  => false,
        ), 'Order Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
		'nullable'  => false,
        ), 'Product Id')
    ->addColumn('remind_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Remind At')		
    ->addColumn('sent_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Sent At')
    ->addColumn('sent_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sent Count')		
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Active')
		
    ->addIndex($installer->getIdxName('faonni_lifetimereminder/schedule', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('faonni_lifetimereminder/schedule', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('faonni_lifetimereminder/schedule', array('product_id')),
        array('product_id'))
    ->addIndex($installer->getIdxName('faonni_lifetimereminder/schedule', array('remind_at')),
        array('remind_at'))
    ->addIndex($installer->getIdxName('faonni_lifetimereminder/schedule', array('sent_count')),
        array('sent_count'))			
    ->addIndex($installer->getIdxName('faonni_lifetimereminder/schedule', array('is_active')),
        array('is_active'))		
		
    ->addForeignKey($installer->getFkName('faonni_lifetimereminder/schedule', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('faonni_lifetimereminder/schedule', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)			
    ->setComment('Faonni Filter Life Reminder Schedule');
	
$connection->createTable($table);

/**
 * Add column is_remind to table 'sales/order'
*/
if (!$connection->tableColumnExists($installer->getTable('sales/order'), 'is_remind'))
{
    $connection->addColumn(
		$installer->getTable('sales/order'), 
		'is_remind', 
		'SMALLINT(5) UNSIGNED DEFAULT "0" COMMENT "Is Remind"'
	);
}

$installer->endSetup();
