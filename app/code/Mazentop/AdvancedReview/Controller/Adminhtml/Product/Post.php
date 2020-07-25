<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Controller\Adminhtml\Product;

use Magento\Review\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
class Post extends ProductController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($data = $this->getRequest()->getPostValue()) {
            /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
            $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            if ($storeManager->hasSingleStore()) {
                $data['stores'] = [
                    $storeManager->getStore(true)->getId(),
                ];
            } elseif (isset($data['select_stores'])) {
                $data['stores'] = $data['select_stores'];
            }
            $review = $this->reviewFactory->create()->setData($data);
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
                        if(isset($up_result['file']) && $up_result['file']){
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
                                //$data['img'][]='pub/media/review/img/'.$up_result['file'];
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
            try {
                $save_result = $review->setEntityId(1) // product
                ->setEntityPkValue($productId)
                    ->setStoreId(Store::DEFAULT_STORE_ID)
                    ->setStatusId($data['status_id'])
                    ->setCustomerId(null)//null is for administrator only
                    ->save();
                //dump($save_result->getData('review_id'));
                /*高级评论信息写入*/
                if($save_result->getData('review_id')){
                    //dump($data);
                    $data['advanced_img'] = '';
                    $data['advanced_video'] = '';
                    if($data['img']){
                        //$data['advanced_img'] =implode('|#|',$data['img']);
                        $data['advanced_img'] = serialize($data['img']);
                    }
                    if($data['video']){
                        $data['advanced_video']=$data['video'];
                    }
                    //dump($data);
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
                /*高级评论信息写入结束*/

                $arrRatingId = $this->getRequest()->getParam('ratings', []);
                foreach ($arrRatingId as $ratingId => $optionId) {
                    $this->ratingFactory->create()
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->addOptionVote($optionId, $productId);
                }

                $review->aggregate();
                $this->messageManager->addSuccess(__('You saved the review.'));
                if ($this->getRequest()->getParam('ret') == 'pending') {
                    $resultRedirect->setPath('review/*/pending');
                } else {
                    $resultRedirect->setPath('review/*/');
                }
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving this review.'));
            }
        }
        $resultRedirect->setPath('review/*/');
        return $resultRedirect;
    }
}
