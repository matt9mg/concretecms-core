<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Notification;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Utility\Service\Identifier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class Events extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make("config");
        $dbConfig = $this->app->make('config/database');
        $enable_server_sent_events = (bool) $config->get('concrete.notification.server_sent_events');
        $this->set('enable_server_sent_events', $enable_server_sent_events);
        if ($enable_server_sent_events) {
            $this->set('publishUrl', $config->get('concrete.notification.mercure.default.publish_url'));
            $this->set('jwtKey', $dbConfig->get('concrete.notification.mercure.default.jwt_key'));
        }
    }

    public function test_connection()
    {
        $ping = $this->request->request->get('ping');
        $publisher = $this->app->make(PublisherInterface::class);
        $update = new Update('https://concretecms/test_connection', json_encode(['pong' => $ping]));
        $publisher($update);
        return new JsonResponse([]);
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {

            $enable_server_sent_events = $this->request->request->get("enable_server_sent_events") ? true : false;
            $config = $this->app->make('config');
            $dbConfig = $this->app->make('config/database');
            $events_previously_enabled = (bool) $config->get('concrete.notification.server_sent_events');
            if ($enable_server_sent_events) {
                if (!$events_previously_enabled) {
                    // Generate a JWT key.
                    $jwtKey = $this->app->make(Identifier::class)->getString(96);
                    $dbConfig->save('concrete.notification.mercure.default.jwt_key', $jwtKey);
                }
                if ($this->request->request->has('publishUrl')) {
                    $config->save('concrete.notification.mercure.default.publish_url', (string)
                        $this->request->request->get('publishUrl'));
                }
            } else {
                $config->save('concrete.notification.mercure', []);
                $dbConfig->save('concrete.notification.mercure', []);
            }
            $config->save('concrete.notification.server_sent_events', $enable_server_sent_events);
            $this->flash('success', t("Server-Sent Events Settings updated successfully."));
            return $this->redirect('/dashboard/system/notification/events');
        }
    }
}
