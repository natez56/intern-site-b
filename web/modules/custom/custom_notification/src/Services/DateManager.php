<?php

namespace Drupal\custom_notification\Services;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class NotificationManager.
 */
class DateManager extends DrupalDateTime implements DateManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDateFromString($date)
    {
        return new DateManager($date);
    }

}
