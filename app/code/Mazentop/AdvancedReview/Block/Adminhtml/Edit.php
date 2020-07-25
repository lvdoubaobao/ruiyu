<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Block\Adminhtml;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit
{
    public function afterGetFormInitScripts($subject,$scr)
    {
        if(!strpos('prototype',$scr)||strpos('Event',$scr)){
            $scr = ltrim($scr,'<script>');
            $scr = rtrim($scr,'</script>');
            $scr ='<script>require(["jquery","prototype"], function(jQuery){'.$scr.'})</script>';
        }
        return $scr;
    }
}
