<?php


namespace Niubi\Form\Controller\VideoForm;


class Index  extends  \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        print_r(111111);exit();
        return $this->_pageFactory->create();
    }
}