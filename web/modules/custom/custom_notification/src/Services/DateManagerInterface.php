<?php

namespace Drupal\custom_notification\Services;

/**
 * Class NotificationManager.
 */
interface DateManagerInterface
{
    /**
     * Get date from date string.
     *
     * @param string $dateString
     */
    public function getDateFromString($date);

}
