<?php


namespace Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'record_id';
    protected $_eventPrefix = 'niubi_abandoned_record_collection';
    protected $_eventObject = 'niubi_abandoned_record_collection';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Niubi\AbandonedCart\Model\AbandonedCart', 'Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart');
    }
}