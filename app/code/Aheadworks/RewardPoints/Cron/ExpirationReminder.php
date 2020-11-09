<?php
/**
 * Copyright 2016 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Cron;

use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\FlagFactory;
use Aheadworks\RewardPoints\Model\Source\NotifiedStatus;
use Aheadworks\RewardPoints\Model\Source\Transaction\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\RewardPoints\Model\TransactionRepository;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Flag;
use Magento\Framework\Stdlib\DateTime\DateTime;


/**
 * Class Aheadworks\RewardPoints\Cron\ExpirationReminder
 */
class ExpirationReminder extends CronAbstract
{
    private $config;

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->isLocked(Flag::AW_RP_EXPIRATION_REMINDER_LAST_EXEC_TIME)) {
            return $this;
        }
        $this->sendExpiredReminder();
        $this->setFlagData(Flag::AW_RP_EXPIRATION_REMINDER_LAST_EXEC_TIME);
    }

    /**
     * Send expired reminder
     *
     * @return $this
     */

    public function __construct(Config $config, DateTime $dateTime, CustomerRewardPointsManagementInterface $customerRewardPointsService, SearchCriteriaBuilder $searchCriteriaBuilder, TransactionRepository $transactionRepository, FlagFactory $flagFactory)
    {
        $this->config = $config;
        parent::__construct($dateTime, $customerRewardPointsService, $searchCriteriaBuilder, $transactionRepository, $flagFactory);
    }

    private function sendExpiredReminder()
    {
        $days = $this->config->getExpirationReminderDays();
        $expireDay = date("Y-m-d H:i:s",strtotime("+". $days ."day"));
        $expireDay2 = date("Y-m-d H:i:s",strtotime("+1 minute",strtotime($expireDay)));

        $this->searchCriteriaBuilder
            ->addFilter(TransactionInterface::STATUS, Status::ACTIVE)
            ->addFilter(TransactionInterface::EXPIRATION_NOTIFIED, NotifiedStatus::WAITING)
            ->addFilter(TransactionInterface::STATUS, Status::ACTIVE)
            ->addFilter(TransactionInterface::EXPIRATION_DATE, $expireDay,'gt')
            ->addFilter(TransactionInterface::EXPIRATION_DATE, $expireDay2,'lt');
//            ->addFilter(TransactionInterface::EXPIRATION_DATE, 'will_expire');

        $willExpireTransactions = $this->transactionRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        $customersData = [];
        foreach ($willExpireTransactions as $willExpireTransaction) {
            $customerId = $willExpireTransaction->getCustomerId();
            if (!isset($customersData[$customerId])) {
                $customersData[$customerId] = [
                    'store_id' => null,
                    'comment' => null,
                    'balance' => $willExpireTransaction->getBalance() + $willExpireTransaction->getBalanceAdjusted(),
                    'expiration_date' => $willExpireTransaction->getExpirationDate(),
                    'notified_status' => NotifiedStatus::NO
                ];
            } else {
                $customersData[$customerId]['balance'] +=
                    $willExpireTransaction->getBalance() + $willExpireTransaction->getBalanceAdjusted();
            }
        }

        $cId = -1;
        foreach ($customersData as $customerId => $customerData) {

            if ($cId == $customerId){
                continue;
            }
            $cId = $customerId;
            $customersData[$customerId]['notified_status'] = $this->customerRewardPointsService->sendNotification(
                $customerId,
                TransactionInterface::EXPIRATION_NOTIFIED,
                $customerData
            );
        }

        foreach ($willExpireTransactions as $willExpireTransaction) {
            $customerId = $willExpireTransaction->getCustomerId();
            $willExpireTransaction->setExpirationNotified($customersData[$customerId]['notified_status']);
            $this->transactionRepository->save($willExpireTransaction);
        }
        return $this;
    }
}
