<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Model\ResourceModel\AdvancedReview;

class AdCollection
{
    public function afterAddStoreFilter($subject)
    {
        $subject->getSelect()->joinLeft(
            ['review_ad' =>  $subject->getTable('review_advanced')],
            'main_table.review_id=review_ad.review_id',
            []
        );
        $subject->getSelect()->columns('review_ad.advanced_img');
        $subject->getSelect()->columns('review_ad.advanced_video');
        return $subject;
    }
}
