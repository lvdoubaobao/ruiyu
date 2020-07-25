<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Model\ResourceModel\AdvancedReview;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mazentop\AdvancedReview\Model\Job', 'Mazentop\AdvancedReview\Model\ResourceModel\AdvancedReview');
    }
}
