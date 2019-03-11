<?php

namespace Drupal\custom_notification\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\custom_notification\Services\NotificationManagerInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Notification' Block.
 *
 * @Block(
 *   id = "notification_block",
 *   admin_label = @Translation("Notifications"),
 *   category = @Translation("Notification Block"),
 * )
 */
class NotificationBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    /**
     * @var $config \Drupal\Core\Config\ConfigFactory
     * @var $notificationManager \Drupal\custom_notification\Services
     * @var $entityTypeManager \Drupal\Core\Entity\EntityTypeManager
     * @var $userData \Drupal\user\UserDataInterface
     * @var $accountProxy Drupal\Core\Session\AccountProxyInterface
     */
    protected $config;
    protected $notificationManager;
    protected $entityTypeManager;
    protected $userData;
    protected $accountProxy;

    /** @var string Config settings */
    const SETTINGS = 'custom_notification.settings.yml';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     *
     * @return static
     */
    public static function create(ContainerInterface $container,
        array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('custom_notification.notification'),
            $container->get('entity_type.manager'),
            $container->get('user.data'),
            $container->get('current_user')
        );
    }

    /**
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     * @param Drupal\custom_notification\Services\NotificationManagerInterface
     * @param Drupal\Core\Entity\EntityTypeManagerInterface
     */
    public function __construct(array $configuration, $plugin_id,
        $plugin_definition, NotificationManagerInterface $notificationManager,
        EntityTypeManagerInterface $entityTypeManager, UserDataInterface $userData,
        AccountProxyInterface $accountProxy) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->notificationManager = $notificationManager;
        $this->entityTypeManager = $entityTypeManager;
        $this->userData = $userData;
        $this->accountProxy = $accountProxy;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheMaxAge()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $entityType = 'node';
        $viewMode = 'teaser';

        if ($this->notificationManager->isNotificationSettingEnabled()) {
            $startDate = $this->notificationManager->getConfigStartDate();
            $endDate = $this->notificationManager->getConfigEndDate();

            $blockContentArray = $this->notificationManager
                ->getRecentThreeNotifications($startDate, $endDate);

            // Reverse array since block shows index 0 at top.
            $blockContentArray = array_reverse($blockContentArray);

            // Check for hidden notifications.
            $currentUser = $this->accountProxy->id();

            // User data key.
            $dataKey = 'hidden_notifications';

            $hiddenNotificationIds = $this->userData->get('custom_notification', $currentUser, $dataKey);
            $hiddenNotificationIds = explode("|", $hiddenNotificationIds);

            $visibleNotifications = [];

            // Check for hidden notifications to ensure they are not added to
            // the final array to be displayed.
            foreach ($blockContentArray as $notification) {
                if (!in_array($notification->get('nid')->value, $hiddenNotificationIds)) {
                    array_push($visibleNotifications, $notification);
                }
            }

            if (empty($visibleNotifications)) {
                return [
                    '#type' => 'markup',
                    '#markup' => $this->t('No new notifications at this time.'),
                ];
            }

            $view_builder = $this->entityTypeManager
                ->getViewBuilder($entityType);

            $build = $view_builder
                ->viewMultiple($visibleNotifications, $viewMode);

            return $build;
        } else {
            return null;
        }

    }

}
