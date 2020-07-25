<?php

/**

 * Copyright 2016 aheadWorks. All rights reserved.

 * See LICENSE.txt for license details.

 */



namespace Aheadworks\RewardPoints\Model\Service;



use Aheadworks\RewardPoints\Api\RewardPointsCartManagementInterface;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;

use Aheadworks\RewardPoints\Model\Config;

use Magento\Quote\Api\CartRepositoryInterface;

use Magento\Framework\Api\CustomAttributesDataInterface;

use Magento\Framework\Exception\CouldNotDeleteException;

use Magento\Framework\Exception\CouldNotSaveException;

use Magento\Framework\Exception\NoSuchEntityException;



/**

 * Class Aheadworks\RewardPoints\Model\Service$RewardPointsCartService

 */

class RewardPointsCartService implements RewardPointsCartManagementInterface

{

    /**

     * @var CustomerRewardPointsManagementInterface

     */

    private $customerRewardPointsService;



    /**

     * @var CartRepositoryInterface

     */

    private $quoteRepository;



    /**

     * @var Config

     */

    private $config;



    /**

     * @var \Magento\Checkout\Model\SessionFactory

     */

    protected $checkoutSessionFactory;



    /**

     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService

     * @param CartRepositoryInterface $quoteRepository

     * @param Config $config

     */

    public function __construct(

        CustomerRewardPointsManagementInterface $customerRewardPointsService,

        CartRepositoryInterface $quoteRepository,

        Config $config,

        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory

    ) {

        $this->customerRewardPointsService = $customerRewardPointsService;

        $this->quoteRepository = $quoteRepository;

        $this->config = $config;

        $this->checkoutSessionFactory = $checkoutSessionFactory;

    }



    /**

     * {@inheritDoc}

     */

    public function get($cartId)

    {

        /** @var  \Magento\Quote\Model\Quote $quote */

        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemsCount()) {

            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));

        }

        return $quote->getAwUseRewardPoints();

    }



    /**

     * {@inheritDoc}

     */

    public function set($cartId, $usedAll = true)

    {

        $checkoutSession = $this->checkoutSessionFactory->create();

        if ($usedAll) {

            $checkoutSession->setData('reward_points_use_all', true);

        }



        /** @var  \Magento\Quote\Model\Quote $quote */

        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemsCount()) {

            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));

        }



        $onceMinBalance = $this->customerRewardPointsService->getCustomerRewardPointsOnceMinBalance(

            $quote->getCustomerId(),

            $quote->getStore()->getWebsiteId()

        );

        if (!$quote->getCustomerId()

            || !$this->customerRewardPointsService->getCustomerRewardPointsBalance($quote->getCustomerId())

            || $onceMinBalance

        ) {

            throw new NoSuchEntityException(__('No reward points to be used'));

        }



        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {

            $quote->setAwUseRewardPoints(true);

            $this->quoteRepository->save($quote->collectTotals());

        } catch (\Exception $e) {

            throw new CouldNotSaveException(__('Could not apply reward points'));

        }


        if (!$quote->getAwUseRewardPoints()) {

            throw new NoSuchEntityException(__('No possibility to use reward points discounts in the cart'));

        }



        $shareCoveredValue = $this->config->getShareCoveredValue($quote->getStore()->getWebsiteId());

        $message = $shareCoveredValue

            ? __(

                'Reward points were successfully applied. '

                . 'Important: It is allowed to cover only %1% of the purchase with Reward Points.',

                $shareCoveredValue

            )

            : __('Reward points were successfully applied.');

        return [

            CustomAttributesDataInterface::CUSTOM_ATTRIBUTES => [

                'success' => true,

                'message' => $message,

                'useAll' => $usedAll,

                'useNumber' => $checkoutSession->getData('reward_points_use_number')

            ]

        ];

    }



    /**

     * {@inheritDoc}

     */

    public function remove($cartId)

    {

        /** @var  \Magento\Quote\Model\Quote $quote */

        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemsCount()) {

            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));

        }



        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {

            $quote->setAwUseRewardPoints(false);

            $this->quoteRepository->save($quote->collectTotals());

        } catch (\Exception $e) {

            throw new CouldNotDeleteException(__('Could not remove reward points'));

        }

        return true;

    }

}

