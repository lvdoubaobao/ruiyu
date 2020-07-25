<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Model;

class AdReview
{
    public function afterLoad($subject)
    {

        $AdvancedReview = \Magento\Framework\App\ObjectManager::getInstance()->get('Mazentop\AdvancedReview\Model\AdvancedReview');
        $AdvancedReview->load($subject->getData('review_id'),'review_id');
        $subject->setData('advanced_img',$AdvancedReview->getData('advanced_img'));
        $subject->setData('advanced_video',$AdvancedReview->getData('advanced_video'));
        return $subject;
    }
}
