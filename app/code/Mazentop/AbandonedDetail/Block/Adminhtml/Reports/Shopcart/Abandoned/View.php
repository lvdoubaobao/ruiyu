<?php
namespace Mazentop\AbandonedDetail\Block\Adminhtml\Reports\Shopcart\Abandoned;

use Magento\Framework\Exception\NoSuchEntityException;

class View extends \Magento\Backend\Block\Template {

    protected $_coreRegistry;
    protected $_quote;
    protected $groupRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->groupRepository = $groupRepository;
        parent::__construct($context, $data);
    }

    public function getQuote(){
        if ($this->_quote == null) {
            $this->_quote = $this->_coreRegistry->registry('quotedetail');
        }
        return $this->_quote;
    }

    public function getBackUrl()
    {
        return $this->getUrl('reports/report_shopcart/abandoned');
    }

    public function getQuoteStoreName()
    {
        if ($storeId = $this->getQuote()->getStoreId()) {
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    public function getCustomerViewUrl()
    {
        if ($this->getQuote()->getCustomerIsGuest() || !$this->getQuote()->getCustomerId()) {
            return '';
        }

        return $this->getUrl('customer/index/edit', ['id' => $this->getQuote()->getCustomerId()]);
    }

    public function getCustomerName()
    {
        if ($this->getQuote()) {
            if ($this->getQuote()->getCustomerFirstname()) {
                $customerName = $this->getQuote()->getCustomerFirstname() . ' ' . $this->getQuote()->getCustomerLastname();
            } else {
                $customerName = (string)__('Guest');
            }
        }else{
            $customerName = (string)__('Guest');
        }
        return $customerName;
    }

    public function getCustomerGroupName()
    {
        if ($this->getQuote()) {
            $customerGroupId = $this->getQuote()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }
    public function getItemsCollection()
    {
        if ($this->getQuote()) {
            //return $this->getQuote()->getItemsCollection();
            return $this->getQuote()->getAllItems();
        }
        return false;
    }
}