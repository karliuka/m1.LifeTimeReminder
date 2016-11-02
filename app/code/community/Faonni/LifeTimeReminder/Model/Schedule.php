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
class Faonni_LifeTimeReminder_Model_Schedule 
	extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_TEMPLATE = 'faonni_lifetimereminder_email_template';
	
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('faonni_lifetimereminder/schedule');
    }

    /**
     * Cancel Schedule
     *
     * @param int $orderId
     * @return Faonni_LifeTimeReminder_Model_Schedule
     */
    public function cancel($orderId)
    {
        $this->getResource()->cancel($orderId);
		
        return $this;
    }

    /**
     * Create Schedule
     *
     * @param $order Mage_Sales_Model_Order
     * @return Faonni_LifeTimeReminder_Model_Schedule
     */
    public function create(Mage_Sales_Model_Order $order)
    {
		$schedules = array();
		$productIds = array();
		
		foreach ($order->getAllVisibleItems() as $item){
			$productIds[] = $item->getProductId();
		}
		
		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('life_time')
			->addAttributeToFilter('entity_id', array('in' => $productIds));
		
		foreach ($collection as $product){
			if (0 < $product->getLifeTime()) {
				$schedules[$product->getId()] = $product->getLifeTime();
			}
		}		
		
		if (0 < count($schedules)) {
			$this->getResource()->create($order->getId(), $schedules);
		}
		
		$this->getResource()->markOrder($order->getId());
		
        return $this;
    }
	
    /**
     * Send reminder emails
     *
     * @return Faonni_LifeTimeReminder_Model_Schedule
     */
    public function sendReminderEmails()
    {
        /** @var $mail Mage_Core_Model_Email_Template */
        $mail = Mage::getModel('core/email_template');

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $identity = Mage::helper('faonni_lifetimereminder')->getEmailIdentity();
		$threshold = Mage::helper('faonni_lifetimereminder')->getSendFailureThreshold();
        $limit = Mage::helper('faonni_lifetimereminder')->getOneRunLimit();

        $recipients = $this->getResource()->getRecipients($limit, $threshold);

		foreach ($recipients as $recipient) {
			
			/* @var $store Mage_Core_Model_Store */
			$store = Mage::getModel('core/store')->load($recipient['store_id']);
			
			/* @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product')
				->setStoreId($store->getId())
				->load($recipient['product_id']);
				
			/* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
				->setFirstname($recipient['customer_firstname'])
				->setLastname($recipient['customer_lastname'])
				->setMiddlename($recipient['customer_middlename'])
				->setPrefix($recipient['customer_prefix'])
				->setSuffix($recipient['customer_suffix'])
				->setEmail($recipient['customer_email']);
				
            $templateVars = array(
                'store'    => $store,
                'product'  => $product,
                'customer' => $customer
            );				
			
            $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
            $mail->sendTransactional(self::XML_PATH_EMAIL_TEMPLATE, $identity,
                $customer->getEmail(), null, $templateVars, $store->getId()
            );

            if ($mail->getSentSuccess()) {
				$this->getResource()->close($recipient['entity_id']);
            } else {
                $this->getResource()->updateSentCount($recipient['entity_id']);
            }			
		}
        $translate->setTranslateInline(true);

        return $this;
    }	
}