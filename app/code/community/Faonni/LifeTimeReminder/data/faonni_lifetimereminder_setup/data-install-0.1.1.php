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

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'life_time', array(
	'attribute_set' => 'Default',
	'group'         => 'General',
	'label'         => 'Life Time (hours)',
	'type'          => 'int',
	'input'         => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible'       => true,
	'required'      => false,
	'user_defined'  => false,
	'default'       => '',
	'apply_to'      => '',
	'sort_order'    => 199,
	'visible_on_front' => false,
	'used_in_product_listing' => false
));