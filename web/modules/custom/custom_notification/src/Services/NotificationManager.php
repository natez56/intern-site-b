<?php

namespace Drupal\custom_notification\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\custom_notification\Services\DateManager;

/**
 * Class NotificationManager.
 */
class NotificationManager implements NotificationManagerInterface
{
    /**
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * @var object[]
     */
    protected $notifications;

    /**
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $configFactory;

    /**
     * @var \Drupal\custom_notification\Services\DateManager
     */
    protected $dateManager;

    /**
     * @var string
     *   Config settings
     */
    const SETTINGS = 'custom_notification.settings.yml';

    /**
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
     * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
     * @param \Drupal\custom_notification\Services\DateManager $dateManager
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager,
        ConfigFactoryInterface $configFactory, DateManager $dateManager) {
        $this->entityTypeManager = $entityTypeManager;
        $this->configFactory = $configFactory;
        $this->dateManager = $dateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllNotifications()
    {
        if (!isset($this->notifications)) {
            $this->loadNotifications();
        }

        return $this->notifications;
    }

    /**
     * Loads notifications and sorts them by date updated.
     */
    protected function loadNotifications()
    {
        $this->notifications = [];

        $nids = $this->entityTypeManager
            ->getStorage('node')
            ->getQuery()
            ->condition('status', 1)
            ->condition('type', 'notification')
            ->execute();

        if ($nids) {
            $this->notifications = $this->entityTypeManager
                ->getStorage('node')
                ->loadMultiple($nids);
        }

        $this->sortNotificationsByDateUpdated();
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationSettingEnabled()
    {
        return $this->configFactory->get(static::SETTINGS)->get('checkbox');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigStartDate()
    {
        return $this->configFactory->get(static::SETTINGS)->get('start');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigEndDate()
    {
        return $this->configFactory->get(static::SETTINGS)->get('end');
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestNotification($startDate = null, $endDate = null)
    {
        if (isset($startDate) || isset($endDate)) {
            return end($this->getNotificationsByDate($startDate, $endDate));
        }

        return end($this->getAllNotifications());
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationCount($startDate = null, $endDate = null)
    {
        if (isset($startDate) || isset($endDate)) {
            return count($this->getNotificationsByDate($startDate, $endDate));
        }

        return count($this->getAllNotifications());
    }

    /**
     * {@inheritdoc}
     */
    public function getRecentThreeNotifications($startDate = null, $endDate = null)
    {
        if (isset($startDate) || isset($endDate)) {
            return array_slice($this->getNotificationsByDate($startDate, $endDate), -3, 3);
        }

        return array_slice($this->getAllNotifications(), -3, 3);
    }

    /**
     * Sorts array by date.
     */
    protected function sortNotificationsByDateUpdated()
    {
        usort($this->notifications, [$this, 'cmpDate']);
    }

    /**
     * Helper function for usort to sort by date.
     *
     * @param object $entityA
     *   Notification entity object.
     * @param object $entityB
     *   Notification entity object.
     * @return int
     *   Int used as the return value that usort will receive.
     */
    protected function cmpDate($entityA, $entityB)
    {
        $aDate = $entityA->get('changed')->value;
        $bDate = $entityB->get('changed')->value;

        if ($aDate == $bDate) {
            return 0;
        }

        return ($aDate < $bDate) ? -1 : 1;
    }

    /**
     * Use config settings to look for valid notifications that have created
     * dates between the config settings dates.
     *
     * @param string $startDate
     *   Date string
     * @param string $endDate
     *   Date string
     * @return object[]
     *   Array of notification entity objects.
     */
    protected function getNotificationsByDate($startDate = null, $endDate = null)
    {
        $validNotifications = [];

        if (isset($startDate)) {
            $startDate = $this->dateManager->getDateFromString($startDate);
        }

        if (isset($endDate)) {
            $endDate = $this->dateManager->getDateFromString($endDate);
        }

        foreach ($this->getAllNotifications() as $notification) {
            $createdDate = $this->dateManager->createFromTimestamp($notification
                    ->get('created')->value);

            if (isset($startDate) && isset($endDate)) {
                if ($createdDate > $startDate && $createdDate < $endDate) {
                    array_push($validNotifications, $notification);
                }
            } else if (isset($startDate)) {
                if ($createdDate > $startDate) {
                    array_push($validNotifications, $notification);
                }
            } else {
                if ($createdDate < $endDate) {
                    array_push($validNotifications, $notification);
                }
            }

        }
        return $validNotifications;
    }
}
