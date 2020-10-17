<?php


namespace Niubi\AbandonedCart\Model;


class AbandonedCart extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'niubi_abandoned_record';

    protected $_cacheTag = 'niubi_abandoned_record';
    protected $_idFieldName = 'record_id';
    protected $_eventPrefix = 'niubi_abandoned_record';
    protected function _construct()
    {
        $this->_init('Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart');
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}