<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Block;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Form
{
    public function beforeSetTemplate($subject, $fileName)
    {
        return ['Mazentop_AdvancedReview::form.phtml'];
    }
}
