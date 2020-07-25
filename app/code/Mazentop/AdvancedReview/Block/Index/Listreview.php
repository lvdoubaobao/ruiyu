<?php
namespace Mazentop\AdvancedReview\Block\Index;

class Listreview extends \Magento\Framework\View\Element\Template
{
    protected $_collectionFactory;
    protected $_collection;
    protected $_pageSize = 5;
    protected $_helper;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory $collectionFactory,
        \Mazentop\AdvancedReview\Helper\Data $helper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getReviews()
    {
        if (!$this->_collection) {
            $this->_collection = $this->_collectionFactory->create();
            
            $this->_collection->getSelect()->joinLeft(
                ['review_advanced' => $this->_collection->getTable('review_advanced')],
                'rt.review_id = review_advanced.review_id',
                ['review_advanced.advanced_img']
            )->where('review_advanced.advanced_img != \'a:0:{}\'')
            ->group('rt.entity_pk_value');

            $this->_collection
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setDateOrder()
                ->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                )
                ->setPageSize($this->_pageSize);
        }
        return $this->_collection;
    }

    public function getReviewFeaturedImage($string)
    {
        $review_image = $this->_helper->getReviewImages($string);
        $featured_image = '';
        if ($review_image) {
            foreach ($review_image as $value) {
                if ($value['is_featured'] == 1) {
                    $featured_image = $value['img'];
                }
            }
            if (!$featured_image) {
                $featured_image = $review_image[0]['img'];
            }
        }
        return $featured_image;
    }
}