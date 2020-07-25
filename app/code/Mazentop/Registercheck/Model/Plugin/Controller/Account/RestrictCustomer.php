<?php

namespace Mazentop\Registercheck\Model\Plugin\Controller\Account;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RestrictCustomer
{

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * RestrictCustomerEmail constructor.
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
		ScopeConfigInterface $scopeConfig

    )
    {
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
		$this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Closure $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure $proceed
    )
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $email = $subject->getRequest()->getParam('email');
        $firstname = $subject->getRequest()->getParam('firstname');
        $lastname = $subject->getRequest()->getParam('lastname');

        list($nick, $domain) = explode('@', $email, 2);		
		
		$domains = $this->scopeConfig->getValue('registercheck/domains/domains', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(!$domains) { 
			return $proceed; 
		}
		
		$domainArray = array_map('trim', explode(',', $domains));
		if(count($domainArray) < 1) { 
			return $proceed; 
		}
		
        if (in_array($domain, $domainArray, true)) {

			$message = $this->scopeConfig->getValue('registercheck/domains/message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			if(!$message) { $message = __('We do not allow registration from your email domain'); }
            $this->messageManager->addErrorMessage($message);
            $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setUrl($defaultUrl);
        }
        if(strlen($firstname) > 150 || strlen($lastname) > 150){
            $message = __('We do not allow illegal usernames');
            $this->messageManager->addErrorMessage($message);
            $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setUrl($defaultUrl);
        }
        return $proceed();
    }
}