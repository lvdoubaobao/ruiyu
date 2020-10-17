<?php


namespace Niubi\Form\Model\ResourceModel;


class VideoPost extends  \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('niubi_video_form', 'form_id');
    }
}