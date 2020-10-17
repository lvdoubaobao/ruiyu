<?php


namespace Niubi\AbandonedCart\Model\ResourceModel;


class AbandonedCart extends  \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('niubi_abandoned_record', 'record_id');
    }
}