<?php

namespace App\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class ViewReservationForDate extends ReservationAction
{
    protected function action(): Response
    {
        $date = $this->request->getQueryParams()['date'] ?? date('Y-m-d');
        $result = $this->repository->getAvailabilityForDate($date);
        return $this->respondWithData(array_values($result));
    }
}
