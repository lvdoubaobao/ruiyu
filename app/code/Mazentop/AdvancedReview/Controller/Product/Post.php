<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Controller\Product;

use Magento\Review\Controller\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Model\Review;
use Magento\Framework\App\Filesystem\DirectoryList;
class Post extends ProductController
{
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data = $this->getRequest()->getPostValue();
            $rating = $this->getRequest()->getParam('ratings', []);
        }
        //$data['img']=array();
        //var_dump($data);
        //var_dump($_FILES['img']);
       // exit;
        /*图片上传处理*/
        $data['img']=array();
        if(isset($_FILES['img'])&&!empty($_FILES['img'])):
            $img_file=array();
            foreach($_FILES['img']['tmp_name'] as $k=>$v){
                if($v){
                    $img_file[$k]['tmp_name'] = $v;
                    $img_file[$k]['name'] = $_FILES['img']['name'][$k];
                    $img_file[$k]['type'] = $_FILES['img']['type'][$k];
                    $img_file[$k]['error'] = $_FILES['img']['error'][$k];
                    $img_file[$k]['size'] = $_FILES['img']['size'][$k];                    
                }
            }
            if($img_file):
                /* @var $filesystem \Magento\Framework\Filesystem */
                $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem' );
                $dir = $filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('review/img');
                $uploader = $this->_objectManager->create(
                    'Mazentop\AdvancedReview\Model\Uploader',
                    ['fileId' => $img_file[0]]
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $fileName =uniqid().'.'.$uploader->getFileExtension();
                try{
                    $up_result = $uploader->save($dir, $fileName);
                    if(isset($up_result['file'])&&$up_result['file']){
                        //$data['img'][]='pub/media/review/img/'.$up_result['file'];
                        $data['img'][]=[
                            'img' => 'pub/media/review/img/'.$up_result['file'],
                            'sort' => 1,
                            'is_featured' => 0
                        ];
                    }
                } catch(\Exception $e) {
                    $this->messageManager->addError(__('Img 1 Upload failed'));
                }
                array_shift($img_file);
                $i = 2;
                foreach($img_file as $v){
                    $uploader->setUploadFileId($v);
                    $fileName =$i.'_'.uniqid().'.'.$uploader->getFileExtension();
                    try{
                        $up_result = $uploader->save($dir, $fileName);
                        if(isset($up_result['file'])&&$up_result['file']){
                            $data['img'][]=[
                                'img' => 'pub/media/review/img/'.$up_result['file'],
                                'sort' => $i,
                                'is_featured' => 0
                            ];
                        }
                    } catch(\Exception $e) {
                        $this->messageManager->addError(__('Img '.$i.' Upload failed'));
                    }
                    $i++;
                }
            endif;
        endif;
        /*图片上传处理结束*/
        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var \Magento\Review\Model\Review $review */
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');
            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $save_result = $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Review::STATUS_PENDING)
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();

                    if($save_result->getData('review_id')){
                        $data['advanced_img'] = '';
                        $data['advanced_video'] = '';
                        if($data['img']){
                            //$data['advanced_img'] =implode('|#|',$data['img']);
                            $data['advanced_img'] = serialize($data['img']);
                        }
                        if($data['video']){
                            $data['advanced_video']=$data['video'];
                        }
                        if($data['advanced_img']||$data['advanced_video']){
                            $data['review_id'] =$save_result->getData('review_id');
                            /** @var \Mazentop\AdvancedReview\Model\AdvancedReview $AdvancedReview */
                            $AdvancedReview = $this->_objectManager->get('Mazentop\AdvancedReview\Model\AdvancedReview' );
                            $AdvancedReview->setData($data);
                            $AdvancedReview->unsetData('id');
                            try{
                                $AdvancedReview->save();
                            } catch(\Exception $e) {
                                $this->messageManager->addError(__('Img and Video Save failed'));
                            }
                        }
                    }
                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }
                    $review->aggregate();
                    $this->messageManager->addSuccess(__('You submitted your review for moderation.'));
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            } else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                } else {
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            }
        }
        $redirectUrl = $this->reviewSession->getRedirectUrl(true);
        $resultRedirect->setUrl($redirectUrl ?: $this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}