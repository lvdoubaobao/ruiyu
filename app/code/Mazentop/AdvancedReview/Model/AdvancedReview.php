<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Core file uploader model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Mazentop\AdvancedReview\Model;
use \Magento\Framework\Model\AbstractModel;

class AdvancedReview extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Mazentop\AdvancedReview\Model\ResourceModel\AdvancedReview');
    }
}