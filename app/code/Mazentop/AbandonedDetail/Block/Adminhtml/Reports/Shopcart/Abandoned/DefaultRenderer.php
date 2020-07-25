<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AbandonedDetail\Block\Adminhtml\Reports\Shopcart\Abandoned;

use Magento\Sales\Model\Order\Item;

/**
 * Adminhtml sales order item renderer
 */
class DefaultRenderer extends \Mazentop\AbandonedDetail\Block\Adminhtml\Reports\Shopcart\Abandoned\Items
{
    /**
     * Get order item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}