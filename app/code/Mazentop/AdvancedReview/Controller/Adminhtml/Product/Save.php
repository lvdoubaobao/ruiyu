<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AdvancedReview\Controller\Adminhtml\Product;

use Magento\Review\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends ProductController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (($data = $this->getRequest()->getPostValue()) && ($reviewId = $this->getRequest()->getParam('id'))) {
            $review = $this->reviewFactory->create()->load($reviewId);
            if (!$review->getId()) {
                $this->messageManager->addError(__('The review was removed by another user or does not exist.'));
            } else {
                try {
                    $review->addData($data)->save();
                    $AdvancedReview = $this->_objectManager->get('Mazentop\AdvancedReview\Model\AdvancedReview');
                    $AdvancedReview->load($review->getData('review_id'),'review_id');
                    $Ad_data['advanced_video'] = $data['advanced_video'];
                    $Ad_data['advanced_img'] = $AdvancedReview->getData('advanced_img');

                    if ($Ad_data['advanced_img']) {
                        $advanced_img = unserialize($Ad_data['advanced_img']);
                    }else{
                        $advanced_img = [];
                    }

                    $uploader = '';
                    for($i=1;$i<4;$i++){
                        if(isset($data['del_ad_img'.$i])&&$data['del_ad_img'.$i]){
                            //need add del image function
                            unset($advanced_img[$i-1]);
                        }
                        if(isset($_FILES['ad_img'.$i]) && $_FILES['ad_img'.$i] && $_FILES['ad_img'.$i]['error']===0){
                            if (!$uploader) {
                                $uploader = $this->_objectManager->create(
                                    'Mazentop\AdvancedReview\Model\Uploader',
                                    ['fileId' =>$_FILES['ad_img'.$i]]
                                );
                                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                                $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem' );
                                $dir = $filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('review/img');
                            } else {
                                $uploader->setUploadFileId($_FILES['ad_img'.$i]);
                            }
                            $fileName = uniqid().'.'.$uploader->getFileExtension();
                            try{
                                $up_result = $uploader->save($dir, $fileName);
                                if(isset($up_result['file'])&&$up_result['file']){
                                    $advanced_img[$i-1]['img']= 'pub/media/review/img/'.$up_result['file'];
                                }
                            } catch(\Exception $e) {
                                $this->messageManager->addError(__('Img '.($i-1).' Upload failed'));
                            }
                        }
                        if (isset($advanced_img[$i-1]['img'])) {

                            $advanced_img[$i-1]['sort'] = isset($data['ad_img_sort'][$i-1]) ? $data['ad_img_sort'][$i-1] : 0;
                            $advanced_img[$i-1]['is_featured'] = (isset($data['ad_img_featured'][0]) && $data['ad_img_featured'][0] == $i) ? 1 : 0;
                        }
                    }

                    $advanced_img = array_values($advanced_img);
                    $advanced_img = serialize($advanced_img);

                    if(!(
                        $advanced_img == $Ad_data['advanced_img'] && 
                        $Ad_data['advanced_video'] == $AdvancedReview->getData('advanced_video'
                        ))
                    ){
                        $Ad_data['review_id'] = $review->getData('review_id');
                        $Ad_data['advanced_img'] = $advanced_img;
                        $AdvancedReview->addData($Ad_data)->save();
                    }
                    $arrRatingId = $this->getRequest()->getParam('ratings', []);
                    /** @var \Magento\Review\Model\Rating\Option\Vote $votes */
                    $votes = $this->_objectManager->create('Magento\Review\Model\Rating\Option\Vote')
                        ->getResourceCollection()
                        ->setReviewFilter($reviewId)
                        ->addOptionInfo()
                        ->load()
                        ->addRatingOptions();
                    foreach ($arrRatingId as $ratingId => $optionId) {
                        if ($vote = $votes->getItemByColumnValue('rating_id', $ratingId)) {
                            $this->ratingFactory->create()
                                ->setVoteId($vote->getId())
                                ->setReviewId($review->getId())
                                ->updateOptionVote($optionId);
                        } else {
                            $this->ratingFactory->create()
                                ->setRatingId($ratingId)
                                ->setReviewId($review->getId())
                                ->addOptionVote($optionId, $review->getEntityPkValue());
                        }
                    }

                    $review->aggregate();

                    $this->messageManager->addSuccess(__('You saved the review.'));
                } catch (LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving this review.'));
                }
            }

            $nextId = (int)$this->getRequest()->getParam('next_item');
            if ($nextId) {
                $resultRedirect->setPath('review/*/edit', ['id' => $nextId]);
            } elseif ($this->getRequest()->getParam('ret') == 'pending') {
                $resultRedirect->setPath('*/*/pending');
            } else {
                $resultRedirect->setPath('review/*/edit', ['id' => $reviewId]);
            }
            return $resultRedirect;
        }
        $resultRedirect->setPath('review/*/');
        return $resultRedirect;
    }
}
