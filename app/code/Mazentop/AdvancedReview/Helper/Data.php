<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Mazentop\AdvancedReview\Helper;

/**
 * Default review helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getVideo($videosrc){
        if(strpos($videosrc, 'youtube.com/watch')!==false&&strpos($videosrc, 'iframe')===false){
            $videoid =  explode('?v=',$videosrc);
            $videoid =$videoid[1];
            $rode =  '<iframe width="604" height="340" frameborder="0" src="https://www.youtube.com/embed/'.$videoid.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }elseif(strpos($videosrc, 'youtu.be/')!==false&&strpos($videosrc, 'iframe')===false){
            $videoid =  explode('be/',$videosrc);
            $videoid =$videoid[1];
            $rode = '<iframe width="604" height="340" src="https://www.youtube.com/embed/'.$videoid.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }elseif(strpos($videosrc, 'vimeo.com')!==false&&strpos($videosrc, 'iframe')===false){
            $videoid =  explode('vimeo.com/',$videosrc);
            $videoid = $videoid[1];
            $rode = '<iframe width="604" height="340" src="https://player.vimeo.com/video/'.$videoid.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }elseif(strpos($videosrc, 'iframe')!==false){
            $rode = $videosrc;
        }elseif(strpos($videosrc, 'embed')!==false){
            $rode = $videosrc;
        }else{
            $rode = '';
        };
        return $rode;
    }

    public function getReviewImages($string)
    {
        $review_image = unserialize($string);
        $sort_arr = [];
        if (is_array($review_image)) {
            foreach ($review_image as $_image) {
                $sort_arr[] = $_image['sort'];
            }
            array_multisort($sort_arr, SORT_DESC, $review_image);
        }
        return $review_image;
    }
}
