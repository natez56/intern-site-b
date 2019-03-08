<?php

namespace Drupal\custom_notification\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\custom_notification\Services\NotificationManagerInterface;
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
     */
    protected $config;
    protected $notificationManager;
    protected $entityTypeManager;

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
            $container->get('entity_type.manager')
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
        EntityTypeManagerInterface $entityTypeManager) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->notificationManager = $notificationManager;
        $this->entityTypeManager = $entityTypeManager;
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

            if (empty($blockContentArray)) {
                return [
                    '#type' => 'markup',
                    '#markup' => $this->t('No notifications at this time.'),
                ];
            }

            // Reverse array since block shows index 0 at top.
            $blockContentArray = array_reverse($blockContentArray);

            $view_builder = $this->entityTypeManager
                ->getViewBuilder($entityType);

            $build = $view_builder
                ->viewMultiple($blockContentArray, $viewMode);

            return $build;
        } else {
            return null;
        }

    }

}
