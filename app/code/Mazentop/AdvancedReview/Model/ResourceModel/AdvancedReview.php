<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Model\ResourceModel;


class AdvancedReview extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('review_advanced', 'id');
    }
}
