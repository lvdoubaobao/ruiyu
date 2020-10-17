<?php


namespace Niubi\AbandonedCart\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers;

//use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Niubi\AbandonedCart\Model\ResourceModel\Bestsellers\CollectionFactory as BestSellersCollectionFactory;

class BestSeller
{
    public function __construct(

        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        DateTime $dateTime,
        \Magento\Catalog\Helper\Image $imageHelper,
        HttpContext $httpContext,
        //   Collection $bestSellersCollectionFactory
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->_dateTime = $dateTime;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->_imageHelper = $imageHelper;
    }

    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getProductCollection()
    {
        $productIds = [];
        //本月第一天
        $beginDate = date('Y-m-d 00:00:00', time());
        //本月最后一天
        $endDate = date('Y-m-d 00:00:00', strtotime("$beginDate  -30 day"));

        $bestSellers = $this->_bestSellersCollectionFactory->create();

        /**
         * @var  \Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection $bestSellers
         */
        $bestSellers->setPeriod('month');;
        //$bestSellers ->setDateRange($endDate,$beginDate);
        foreach ($bestSellers as $k => $product) {
            /**
             * @var  Bestsellers $product
             */
            if (!in_array($product->getProductId(), [445, 782])) {
                //     $productIds[] = $product->getProductId().'s:'.$product->getData('period');
                $productIds[$k]['id'] = $product->getProductId();
                $productIds[$k]['period'] = $product->getData('period');
                $productIds[$k]['qty_ordered'] = $product->getData('qty_ordered');
                $productIds[$k]['product_name'] = $product->getData('product_name');
            }
        }

        $productIds = array_reverse($productIds);
        $period = [];
        $qty_ordered = [];
        foreach ($productIds as $k => $row) {
            $period[$k] = $row['period'];
            $qty_ordered[$k] = $row['qty_ordered'];
        }

        //   array_multisort($qty_ordered,SORT_DESC,$period,SORT_DESC,$productIds);
        array_multisort($period, SORT_DESC, $qty_ordered, SORT_DESC, $productIds);
        $ids = [];
        foreach ($productIds as $item) {
            if (!in_array($item['id'], $ids)) {
                $ids[] = $item['id'];
            }
        }

        $productIds = array_unique($ids);
        $productIds = array_slice($productIds, 0, 6);
       // $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $products = [];
        foreach ($productIds as $k => $item) {
            $_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item);
            $products[$k]['id'] = $_product->getId();
            $products[$k]['price'] = $_product->getFinalPrice();
            $products[$k]['name'] = $_product->getName();
            $products[$k]['url'] = $_product->getProductUrl();
            if (@$_product->getImage()) {
                $products[$k]['image'] = $this->_imageHelper->init($_product->getImage(), 'small_image')
                    ->setImageFile($_product->getImage()) // image,small_image,thumbnail
                    ->resize(380)
                    ->getUrl();
            } else {
                $products[$k]['image'] = '';
            }
        }
        return $products;
    }
}