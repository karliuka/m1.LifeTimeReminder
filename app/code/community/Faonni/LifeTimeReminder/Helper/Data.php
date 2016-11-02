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
class Faonni_LifeTimeReminder_Helper_Data 
	extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ENABLED = 'promo/faonni_lifetimereminder/enabled';
    const XML_PATH_SEND_LIMIT = 'promo/faonni_lifetimereminder/limit';
    const XML_PATH_EMAIL_IDENTITY = 'promo/faonni_lifetimereminder/identity';
    const XML_PATH_EMAIL_THRESHOLD = 'promo/faonni_lifetimereminder/threshold';

    /**
     * Check whether reminder rules should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Return maximum emails that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_SEND_LIMIT);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY);
    }

    /**
     * Return email send failure threshold
     *
     * @return int
     */
    public function getSendFailureThreshold()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_EMAIL_THRESHOLD);
    }
}