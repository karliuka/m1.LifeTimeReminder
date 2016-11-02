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
class Faonni_LifeTimeReminder_Model_Observer
{
    const CRON_MINUTELY = 'I';
    const CRON_HOURLY   = 'H';
    const CRON_DAILY    = 'D';

    /**
     * Return array of cron frequency types
     *
     * @return array
     */
    public function getCronFrequencyTypes()
    {
        return array(
            self::CRON_MINUTELY => Mage::helper('cron')->__('Minute Intervals'),
            self::CRON_HOURLY   => Mage::helper('cron')->__('Hourly'),
            self::CRON_DAILY    => Mage::helper('cron')->__('Daily')
        );
    }

    /**
     * Return array of cron valid munutes
     *
     * @return array
     */
    public function getCronMinutes()
    {
        return array(
			1  => Mage::helper('cron')->__('1 minutes (test mode)'),
            5  => Mage::helper('cron')->__('5 minutes'),
            10 => Mage::helper('cron')->__('10 minutes'),
            15 => Mage::helper('cron')->__('15 minutes'),
            20 => Mage::helper('cron')->__('20 minutes'),
            30 => Mage::helper('cron')->__('30 minutes')
        );
    }

    /**
     * Cancel order
     *
     * @param Varien_Event_Observer $observer	 
     * @return Faonni_LifeTimeReminder_Model_Observer
     */
    public function orderCancel($observer)
    {
        /** @var $quote Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getOrder();
		Mage::getModel('faonni_lifetimereminder/schedule')->cancel($order->getId());
		
		return $this;
    }

    /**
     * Save order
     *
     * @param Varien_Event_Observer $observer	 
     * @return Faonni_LifeTimeReminder_Model_Observer
     */
    public function orderSave($observer)
    {
        /** @var $quote Mage_Sales_Model_Order */
		$order = $observer->getEvent()->getOrder();
		if (Mage::helper('faonni_lifetimereminder')->isEnabled() && 
			$order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE && 
				!$order->getIsRemind()) {
					Mage::getModel('faonni_lifetimereminder/schedule')->create($order);
		}
		return $this;
    }
	
    /**
     * Send scheduled notifications
     *
     * @return Faonni_LifeTimeReminder_Model_Observer
     */
    public function scheduledNotification()
    {
		if (Mage::helper('faonni_lifetimereminder')->isEnabled()) {
            Mage::getModel('faonni_lifetimereminder/schedule')->sendReminderEmails();
        }
		return $this;
    }	
}