<?php


namespace Niubi\Form\Controller\adminhtml\Video;


use Niubi\Form\Model\ResourceModel\VideoForm\CollectionFactory;

class Delete  extends  \Magento\Framework\App\Action\Action
{
    protected  $_videoFormFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        CollectionFactory $_videoFormFactory
    )
    {
        $this->_videoFormFactory=$_videoFormFactory;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $params=$this->getRequest()->getParams();
        $factory=$this->_videoFormFactory->create();
        $videoForm= $factory->addFieldToFilter('form_id',['in'=>$params['selected']]);
        foreach ($videoForm as $item){

            $item->delete();
        }
        $this->messageManager->addSuccessMessage('delete success');
        $this->_redirect($this->_redirect->getRefererUrl());

    }
}