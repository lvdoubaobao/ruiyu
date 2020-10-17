<?php


namespace Niubi\AbandonedCart\Controller\adminhtml\email;


class Review extends  \Magento\Framework\App\Action\Action
{
    protected  $_data;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Niubi\Core\Helper\Data $data
    )
    {
        $this->_data=$data;
        return parent::__construct($context);
    }
    public function execute()
    {
        $tel= $this->_request->getParam('tel');
        $desc= $this->_data->getAbandonedConfig($tel);
        print_r($desc);
    }


}