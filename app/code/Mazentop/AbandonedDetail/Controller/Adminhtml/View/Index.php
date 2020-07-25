<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mazentop\AbandonedDetail\Controller\Adminhtml\View;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

class Index extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    protected $_quote;
    protected $_quoteDetail;
    protected $resultPageFactory;
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        //\Magento\Reports\Model\ResourceModel\Quote\Collection $quotecollection,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_quote = $quote;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Abandoned carts action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('quote_entity_id');

        try {
            $this->_quoteDetail = $this->_quote->load($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        //var_dump($this->_quoteDetail->getData());
        $this->_coreRegistry->register('quotedetail', $this->_quoteDetail);

        //$this->_view->getPage()->getConfig()->getTitle()->prepend(__('Abandoned Carts Detail'));
        //$this->_view->renderLayout();
        return  $resultPage = $this->resultPageFactory->create();
    }
}