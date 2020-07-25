<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Block\Adminhtml\Edit;

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
        $coreRegistry = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry');
        $value = $coreRegistry->registry('review_data')->getData();
        $baseurl  = $subject->getBaseUrl();
        $advanced_img = '';
        $advanced_video = '';
        $img_count=1;
        $delete_text = __('Delete');
        if ($value['advanced_img']) {
            $advanced_data = unserialize($value['advanced_img']);
            foreach($advanced_data as $v){
                if($v):
                $_checked = $v['is_featured'] == 1 ? ' checked' : '';
                $advanced_img.= '<span><a target="_blank" href="'.$baseurl.str_replace("pub/media/review","media/review",$v['img']).'"><img src="'.$baseurl.str_replace("pub/media/review","media/review",$v['img']).'"/></a><br/>'.__('Order').' : <input name="ad_img_sort[]" type="text" value="'.$v['sort'].'" style="width:50px;" /><br/>'.__('Featured').' : <input type="radio" value="'.$img_count.'" name="ad_img_featured[]"'.$_checked.' /><br/>'.$delete_text.' : <input name="del_ad_img'.$img_count.'" type="checkbox" value="1" /></span>';
                $img_count++;
                endif;
            }
        }
        $advanced_img_core = '<div><span><input type="file" name="ad_img1"/></span><span><input type="file" name="ad_img2"/></span><span><input type="file" name="ad_img3"/></span></div>';
        if($value['advanced_video']){
            $data = \Magento\Framework\App\ObjectManager::getInstance()->get('Mazentop\AdvancedReview\Helper\Data');
            $advanced_video = '<div class="re_video"><div class="clickdiv"></div>';
            $advanced_video .= $data->getVideo($value['advanced_video']);
            $advanced_video .= '</div>';
        }
        $fieldset = $form-> getElements()->searchById('review_details');
        $fieldset->addField(
            'adreview_img',
            'note',
            [
                'label' => __('Review Img'),
                'text' =>$advanced_img.$advanced_img_core
            ]
        );
       $fieldset->addField(
            'advanced_video',
           'text',
            ['label' => __('Video'), 'required' => false, 'name' => 'advanced_video']
        );
        if($advanced_video){
            $fieldset->addField(
                'adreview_video',
                'note',
                [
                    'label' => __(''),
                    'text' =>$advanced_video
                ]
            );
        }
        $form->addCustomAttribute('enctype','multipart/form-data');
        $form->setValues($value);
    }
}
