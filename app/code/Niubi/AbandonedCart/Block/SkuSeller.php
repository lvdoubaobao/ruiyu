<?php


namespace Niubi\AbandonedCart\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\DateTime\DateTime;

class SkuSeller
{
    public function __construct(

        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        DateTime $dateTime,
        \Magento\Catalog\Helper\Image $imageHelper,
        HttpContext $httpContext,

        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->_dateTime = $dateTime;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_imageHelper = $imageHelper;
    }

    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getProductCollection($str)
    {

        preg_match_all('/{{sku:([\s\S]*?)}}/', $str, $matches);



        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
         */
        if (empty($matches[1])) {
            return [];
        }
        $products = [];
        foreach ($matches[1] as $key=> $match){
            $productCollection = $this->_productCollectionFactory->create();
            $skus=explode(',',$match);
            $productCollection->addFieldToFilter('sku', ['in' => $skus]);

            foreach ($productCollection as $k => $item) {

                $_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getId());

                $products[$key][$k]['id'] = $_product->getId();
                $products[$key][$k]['price'] = $_product->getFinalPrice();
                $products[$key][$k]['name'] = $_product->getName();
                $products[$key][$k]['url'] = $_product->getProductUrl();
                if (@$_product->getImage()) {
                    $products[$key][$k]['image'] = $this->_imageHelper->init($_product->getImage(), 'small_image')
                        ->setImageFile($_product->getImage()) // image,small_image,thumbnail
                        ->resize(380)
                        ->getUrl();
                } else {
                    $products[$key][$k]['image'] = '';
                }
            }
        }

        return $products;
    }
}