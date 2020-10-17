<?php


namespace Niubi\Sales\Controller\Guest;


class Form extends \Magento\Sales\Controller\Guest\Form
{
    /**
     * Order view form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Orders'));
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($resultPage);
        return $resultPage;
    }
}