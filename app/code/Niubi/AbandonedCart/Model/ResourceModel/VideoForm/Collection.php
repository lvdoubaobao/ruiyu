<?php


namespace Niubi\Form\Model\ResourceModel\VideoForm;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'form_id';
    protected $_eventPrefix = 'niubi_form_videoForm_collection';
    protected $_eventObject = 'VideoForm_collection';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Niubi\Form\Model\VideoForm', 'Niubi\Form\Model\ResourceModel\VideoForm');
    }
}