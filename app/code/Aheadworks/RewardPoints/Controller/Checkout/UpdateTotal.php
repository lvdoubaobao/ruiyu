<?php
namespace Aheadworks\RewardPoints\Controller\Checkout;

use \Magento\Framework\Controller\Result\JsonFactory;
use Aheadworks\RewardPoints\Model\Service\RewardPointsCartService;
use Magento\Framework\Registry;

class UpdateTotal extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultJsonFactory;

    /**
     * @var \Aheadworks\RewardPoints\Model\Service\RewardPointsCartService;
     */
    protected $rewardPointsCartService;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSessionFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        RewardPointsCartService $rewardPointsCartService,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->rewardPointsCartService = $rewardPointsCartService;
        $this->checkoutSessionFactory = $checkoutSessionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $this->checkoutSessionFactory->create()->setData('reward_points_use_number', (int) $data['reward_points']);
        //$this->registry->register('reward_points_use_number', (int) $data['reward_points']);
        if ($data['reward_points'] == 0) {
            $response = $this->rewardPointsCartService->remove((int) $data['quote_id']);
        } else {
            $response = $this->rewardPointsCartService->set($data['quote_id'], false);
        }

        return $result->setData($response);
    }
}