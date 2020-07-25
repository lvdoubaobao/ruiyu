<?php

namespace Aheadworks\Rewardpoints\Block\Checkout\Cart;

use Magento\Framework\Registry;

class Point extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $coreRegistry
    )
    {
        parent::__construct($context, []);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->coreRegistry = $coreRegistry;
    }

    public function getRewardPointsData()
    {
        var_dump($this->coreRegistry->registry('reward_points_data'));
    }
}