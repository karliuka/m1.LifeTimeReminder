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
class Faonni_LifeTimeReminder_Model_Resource_Schedule 
	extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('faonni_lifetimereminder/schedule', 'entity_id');
    }

    /**
     * Cancel Schedule
     *
     * @param int $orderId
     * @return Faonni_LifeTimeReminder_Model_Resource_Schedule
     */
    public function cancel($orderId)
    {
		$this->_getWriteAdapter()
			->delete($this->getMainTable(), array('order_id = ?' => (int)$orderId));
		
        return $this;
    }

    /**
     * Create Schedule
     *
     * @param int $orderId	 
     * @param array $schedules
     * @return Faonni_LifeTimeReminder_Model_Resource_Schedule
     */
    public function create($orderId, $schedules)
    {
		$data = array();
		foreach ($schedules as $productId => $lifeTime) {
			$data[] = array(
				'order_id'   => (int)$orderId,
				'product_id' => (int)$productId,
				'remind_at'  => (string)Mage::getModel('core/date')->date(null, time() + ($lifeTime * 60 * 60))
			);
		}
		$this->_getWriteAdapter()
			->insertMultiple($this->getMainTable(), $data);
		
        return $this;
    }

    /**
     * Close Schedule
     *
     * @param int $scheduleId	 
     * @return Faonni_LifeTimeReminder_Model_Resource_Schedule
     */
    public function close($scheduleId)
    {
		$adapter = $this->_getWriteAdapter();
		$adapter->update(
            $this->getMainTable(),
            array(
				'sent_at'    => (string)Mage::getModel('core/date')->date(null, time()),
				'is_active'  => '0', 
				'sent_count' => new Zend_Db_Expr('sent_count + 1')
			),
            array($adapter->quoteInto('entity_id = ?', $scheduleId))
        );
		
        return $this;
    }	

    /**
     * Update count
     *
     * @param int $scheduleId	 
     * @return Faonni_LifeTimeReminder_Model_Resource_Schedule
     */
    public function updateSentCount($scheduleId)
    {
		$adapter = $this->_getWriteAdapter();
		$adapter->update(
            $this->getMainTable(),
            array('sent_count' => new Zend_Db_Expr('sent_count + 1')),
            array($adapter->quoteInto('entity_id = ?', $scheduleId))
        );
		
        return $this;
    }	
	
    /**
     * Mark order
     *
     * @param int $orderId	 
     * @return Faonni_LifeTimeReminder_Model_Resource_Schedule
     */
    public function markOrder($orderId)
    {
		$adapter = $this->_getWriteAdapter();
		$adapter->update(
            Mage::getResourceModel('sales/order')->getMainTable(),
            array('is_remind' => '1'),
            array($adapter->quoteInto('entity_id = ?', $orderId))
        );
		
        return $this;
    }
	
    /**
     * Retrieve list of customers for notification process. This process can be initialized by system cron.
     *
     * @param int|null $limit
     * @param int|null $threshold	 
     * @return array
     */
    public function getRecipients($limit=null, $threshold=null)
    {
		$adapter = $this->_getReadAdapter();
		$select = $adapter->select()
            ->from(
                array('schedule' => $this->getMainTable()),
                array('entity_id', 'order_id', 'product_id')
            )
			->join(
                array('order' => Mage::getResourceModel('sales/order')->getMainTable()),
                'schedule.order_id = order.entity_id',
                array('store_id', 'customer_email', 'customer_firstname', 'customer_lastname', 'customer_middlename', 'customer_prefix', 'customer_suffix')
            )
			->where('schedule.is_active = 1')
			->where('schedule.remind_at <= ?', $this->formatDate(time()));
		
		if ($threshold) {
			$select->where('schedule.sent_count < ?', (int)$threshold);
		}

        if ($limit) {
            $select->limit($limit);
        }
        /** @var $helper Mage_Core_Model_Resource_Helper_Abstract */
        $helper = Mage::getResourceHelper('faonni_lifetimereminder');			
		$sql = $helper->getQueryUsingAnalyticFunction($select);

		return $adapter->fetchAll($sql);
    }	
}