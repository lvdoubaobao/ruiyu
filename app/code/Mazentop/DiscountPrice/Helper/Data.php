<?php

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mazentop\DiscountPrice\Helper;

use Magento\Catalog\Model\Category;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Sales\Model\Order;

/**
 * Catalog data helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_category;
    protected $_wishlistCollectionFactory;
    protected $_storeManager;
    protected $_stockItemRepository;
    protected $_configurable;
    protected $_ordercollectionFactory;
    protected $_order;
    protected $_checkoutSession;
    protected $rules;
    protected $dateHelper;
    protected $imageHelper;

    public function __construct(
            \Magento\Framework\App\Helper\Context $context, 
            Category $category, 
            /*\Magento\Store\Model\StoreManagerInterface $storeManager,*/ 
            \Magento\Catalog\Helper\Category $categoryHelper,
            CollectionFactory $wishlistCollectionFactory,
            \Magento\CatalogInventory\Api\StockStateInterface $stockItemRepository,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
            \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $ordercollectionFactory,
            Order $order,    
            \Magento\Catalog\Helper\Image $imageHelper,       
            \Magento\Checkout\Model\Session $checkoutSession,
            \Magento\Framework\View\Element\Template\Context $context2,
            \Magento\CatalogRule\Model\Rule $rules,
            \Magento\Framework\Stdlib\DateTime\DateTime $dateHelper,
            array $data = []
    ) {
        $this->_category = $category;
        /*$this->_storeManager = $storeManager;*/
        $this->_categoryHelper = $categoryHelper;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_configurable = $configurable;
        $this->_ordercollectionFactory = $ordercollectionFactory;
        $this->_order = $order;
        $this->_checkoutSession = $checkoutSession;
        $this->rules = $rules;
        $this->dateHelper = $dateHelper;
        $this->_imageHelper = $imageHelper;
        parent::__construct($context);
    }

    //获取分类规则或购物车规则的结束日期
    public function getProductDiscountEndDate($_product, $format = 'd F')
    {
        foreach ($this->rules->getResourceCollection() as $rule) {
            if ($rule->getMatchingProductIds()[$_product->getId()][1]) {
                {
                    return $end = $this->dateHelper->date($format, $rule->getToDate());
                }
            }
        }

        return '';
    }

    
    // Use this method to get ID 
    public function getRealOrderId()
    {
         $lastorderId = $this->_checkoutSession->getLastOrderId();
        return $lastorderId;
    }
    public function getOrderInfo($orderId){  //获取订单信息
        $lid = $this->getRealOrderId();
        $orderInfo = array();        
        $currentorder = $this->_order->load($lid);
        $orderInfo['totalprice'] = $currentorder->getGrandTotal();
        $orderInfo['currency'] = $currentorder->getOrderCurrencyCode();
        $items = $currentorder->getAllItems();
        $a = array();
        foreach($items as $i){
            $a[]  = $i->getProductId();          
        }
        $orderInfo['ProductId'] = implode(',',$a);
        return $orderInfo;
    }
    
    public function DisplayDiscountLabel($_product)
    {

        $originalPrice = $_product->getPrice();
        $finalPrice = $_product->getFinalPrice();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = number_format(($originalPrice - $finalPrice) * 100 / $originalPrice,0);
        }

        if ($percentage) {
            return $percentage."%<br/>Off";
        }
    }
    

    public function getStockNum($product) 
    {   
        $configurable_products = $this->_configurable->getUsedProducts(
                $product
        );
        $qty = 0;
        if ($configurable_products) {
            foreach ($configurable_products as &$cproduct) {
                $qty += $this->_stockItemRepository->getStockQty($cproduct->getId(), $cproduct->getStore()->getWebsiteId());
            }
        } else {
            $qty = $this->_stockItemRepository->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
        }
        return $qty;
    }

    function getAllChildrenOfCategory($category) {  //获取当前分类的所有子分类
        $resArr = array(); //结果数组    
        $categoryId = $category->getId();
        $categoryName = $category->getName();
        //获取当前分类的子分类
        $_category = $this->_category->load($categoryId);
        $subcatalog = $_category->getChildrenCategories();
        return $subcatalog;
    }

    function getAllcategory() {  //获取所有子分类
         $resArr = array();     
        $_categories = $this->_categoryHelper->getStoreCategories();
        foreach ($_categories as $key => $subCatid) {
            $resArr[$key]['catename'] = $subCatid->getName();
            $resArr[$key]['url'] = $this->_categoryHelper->getCategoryUrl($subCatid);
            $_subcategory = $this->_category->load($subCatid->getId())->getChildrenCategories();
            if ($_subcategory) {
                foreach ($_subcategory as $k => $_sub) {
                    $resArr[$key]['child'][$k]['subname'] = $_sub->getName();
                    $resArr[$key]['child'][$k]['suburl'] = $_sub ->getUrl();               
                }
            }
        }
        return $resArr;
    }
    
    function getWishCount($productId)
    {
        $wishlist = $this->_wishlistCollectionFactory->create()->addFieldToFilter('product_id',$productId);
        if($wishlist){
            $count = count($wishlist);
        }else{
            $count = 0;
        }        
        return $count;
    }

    public function getImageUrl($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if($product->getProduct()->getId()){
            $product = $objectManager->create('Magento\Catalog\Model\Product')
                       ->load($product->getProduct()->getId());
            $imagewidth=88;
            $imageheight=88;
            $image_url = $this->_imageHelper
                        ->init($product, 'product_page_image_small')
                        ->setImageFile($product->getFile())
                        ->resize($imagewidth, $imageheight)
                        ->getUrl();  
        }else{
            $image_url = $this->_imageHelper
                         ->init($product, 'product_page_image_small')
                         ->getDefaultPlaceholderUrl('small_image');
        }
        return $image_url;
    }
    
    // public function getStoreCurrency()
    // {
    //     return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    // }

    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
