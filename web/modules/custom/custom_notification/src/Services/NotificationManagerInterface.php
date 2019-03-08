<?php

namespace Drupal\custom_notification\Services;

/**
 * Provides an interface defining a notification manager.
 */
interface NotificationManagerInterface
{
    /**
     * Returns an array of all notifications.
     *
     * @return object[]
     *   An array of all notifications
     */
    public function getAllNotifications();

    /**
     * Get notifications enabled information.
     *
     * @return bool
     *   True if notifications are enabled.
     */
    public function isNotificationSettingEnabled();

    /**
     * Get config notification start time settings.
     *
     * @return string
     *   The date string representing the cutoff indicating that dates must
     *   be after this date to be displayed.
     */
    public function getConfigStartDate();

    /**
     * Get config notification end time settings.
     *
     * @return string
     *   The date string representing the cutoff for dates to be displayed.
     *   Dates must be lower that this result to be displayed.
     */
    public function getConfigEndDate();

    /**
     * Get latest updated notification that is published within specified range.
     * If range is not included then return most recent of all notifications.
     *
     * @param string $startDate
     *   Date string
     * @param string $endDate
     *   Date string
     * @return object
     *   Notification entity object
     */
    public function getLatestNotification($startDate, $endDate);
    /**
     * Returns the number of published notifications.
     *
     * @param string $startDate
     *   Date string
     * @param string $endDate
     *   Date string
     * @return int
     *   The number of notifications
     */
    public function getNotificationCount($startDate, $endDate);

    /**
     * Returns array of nodes by updated date and published.
     *
     * @param string $startDate
     *   Date string
     * @param string $endDate
     *   Date string
     * @return object[]
     *   Array of notification entity objects.
     */
    public function getRecentThreeNotifications($startDate, $endDate);
}
