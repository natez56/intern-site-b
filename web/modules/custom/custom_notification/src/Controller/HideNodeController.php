<?php

namespace Drupal\custom_notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Node;
use Drupal\Core\Routing\ResettableStackedRouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Defines HelloController class.
 */
class HideNodeController extends ControllerBase
{
    /**
     * @var \Drupal\Core\Routing\ResettableStackedRouteMatchInterface
     */
    protected $routeMatch;

    /**
     * @var \Drupal\user\UserDataInterface
     */
    protected $userData;

    /**
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $accountProxy;

    /**
     * Constructs a MyController object
     *
     * @param \Drupal\Core\Routing\ResettableStackedRouteMatchInterface
     * @param \Drupal\user\UserDataInterface
     * @param \Drupal\Core\Session\AccountProxyInterface
     */
    public function __construct(
        ResettableStackedRouteMatchInterface $routeMatch,
        UserDataInterface $userData, AccountProxyInterface $accountProxy) {
        $this->routeMatch = $routeMatch;
        $this->userData = $userData;
        $this->accountProxy = $accountProxy;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('current_route_match'),
            $container->get('user.data'),
            $container->get('current_user')
        );
    }

    /**
     * Takes node parameter passed in url and uses userData service to
     * save that node id under the key hidden_notifications to be used as
     * a check for which nodes to display.
     */
    public function content()
    {
        $nid = $this->routeMatch->getParameters();
        $nid = $nid->get('node');

        // Data specific to user that is logged in.
        $currentUserId = $this->accountProxy->id();

        $dataKey = 'hidden_notifications';

        $hiddenNotificationIds = $this->userData
            ->get('custom_notification', $currentUserId, $dataKey);

        // Convert stored string to array.
        $hiddenNotificationIds = explode("|", $hiddenNotificationIds);

        if (isset($hiddenNotificationIds)) {
            if (!in_array($nid, $hiddenNotificationIds)) {
                array_push($hiddenNotificationIds, $nid);
            }
        } else {
            $hiddenNotificationIds = [$nid];
        }

        // Convert array to string to be stored in db.
        $hiddenNotificationIds = implode("|", $hiddenNotificationIds);

        $this->userData->set(
            'custom_notification',
            $currentUserId,
            $dataKey,
            $hiddenNotificationIds
        );

        return new Response();
    }

}
