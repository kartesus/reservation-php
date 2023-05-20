<?php

declare(strict_types=1);

use App\Application\Actions\Reservation\ViewReservationForDate;
use App\Application\Actions\Reservation\MakeReservation;
use App\Application\Actions\Reservation\ConfirmReservation;
use App\Application\Actions\Reservation\CancelReservation;
use App\Application\Actions\Reservation\AddToWaitlist;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/availability', ViewReservationForDate::class);

    $app->get('/emails', function ($request, $response, $args) {
        $db = $this->get('db');
        $stmt = $db->prepare('SELECT * FROM emails');
        $stmt->execute();
        $emails = $stmt->fetchAll();
        $response->getBody()->write(json_encode($emails));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/waitlist', AddToWaitlist::class);

    $app->group('/reservations', function (Group $group) {
        $group->post('', MakeReservation::class);
        $group->delete('/{id}', CancelReservation::class);
        $group->put('/{id}/confirmation', ConfirmReservation::class);
    });
};
