<?php


namespace Niubi\AbandonedCart\Observer;


use Magento\Customer\Model\Session;
use Niubi\AbandonedCart\Model\AbandonedCart;
use Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart\Collection;
use Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart\CollectionFactory;

class CartAdd implements \Magento\Framework\Event\ObserverInterface
{
    protected $_session;
    protected $_abandonedCartCollection;
    protected $_quotesFactory;

    /**
     * CartAdd constructor.
     * @param Session $session
     * @param Collection $abandonedCartCollection
     */
    public function __construct(
        Session $session,
        CollectionFactory $abandonedCartCollection,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quotesFactory
    )
    {
        $this->_session = $session;
        $this->_abandonedCartCollection = $abandonedCartCollection;
        $this->_quotesFactory = $quotesFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //判断是否登录
        $isLoggedIn = $this->_session->isLoggedIn();
        if ($isLoggedIn) {

            //更改弃购状态
            $_quotesFactory = $this->_quotesFactory->create();
            $quote = $_quotesFactory->addFieldToFilter('customer_id', ['eq' => $this->_session->getCustomerId()])->getFirstItem();
            $abandoned_send = $quote->getData('abandoned_send');
            if ($abandoned_send != 0) {
                $quote->setData('abandoned_send', 0);
                $quote->save();
            }

            $cartCollection = $this->_abandonedCartCollection->create();
            $cartCollection->addFieldToFilter('customer_id', ['eq' => $this->_session->getCustomerId()])
               ->addFieldToFilter('is_display', ['eq' =>0]);
            /**
             * @var \Niubi\AbandonedCart\Model\ResourceModel\AbandonedCart $cartCollection
             */
            foreach ($cartCollection as $item){
                $item->setData('is_display',1);
                $item->save();
            }


        }


        return $this;
    }
}