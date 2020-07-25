<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Block\Adminhtml\Add;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Form
{
    public function beforeSetForm($subject, $form)
    {
        $advanced_img_core = '<div><span><input type="file" name="img[]"/></span><span><input type="file" name="img[]"/></span><span><input type="file" name="img[]"/></span></div>';
        $fieldset = $form-> getElements()->searchById('add_review_form');
        $fieldset->addField(
            'adreview_img',
            'note',
            [
                'label' => __('Review Img'),
                'text' =>$advanced_img_core
            ]
        );
       $fieldset->addField(
            'video',
           'text',
            ['label' => __('Video'), 'required' => false, 'name' => 'video']
        );
        $form->addCustomAttribute('enctype','multipart/form-data');
    }
}
