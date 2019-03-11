<?php

namespace Drupal\custom_notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Node;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Defines HelloController class.
 */
class HideNodeController extends ControllerBase
{

    /**
     * Sets node parameter passed in route to published.
     * Redirects back to notification manager when complete.
     */
    public function content()
    {
        $nid = \Drupal::routeMatch()->getParameters();
        $nid = $nid->get('node');

        $userData = \Drupal::service('user.data');
        $currentUser = \Drupal::currentUser()->id();
        $preference = 'hidden_notifications';
        $hiddenNotifications = $userData->get('custom_notification', $currentUser, $preference);
        $hiddenNotifications = explode("|", $hiddenNotifications);

        if (isset($hiddenNotifications)) {
            dsm($hiddenNotifications);
            if (!in_array($nid, $hiddenNotifications)) {
                array_push($hiddenNotifications, $nid);
            }
        } else {
            $hiddenNotifications = [$nid];
        }

        $hiddenNotifications = implode("|", $hiddenNotifications);
        dsm($hiddenNotifications);
        $userData->set('custom_notification', $currentUser, $preference, $hiddenNotifications);

        $build = array(
            '#type' => 'markup',
            '#markup' => t('Hello World!'),
        );
        // This is the important part, because will render only the TWIG template.
        return new Response(render($build));

    }

}
