<?php

namespace App\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class MakeReservation extends ReservationAction
{
    protected function action(): Response
    {
        try {
            $data = $this->request->getParsedBody();
            $id = $this->repository->makeReservation($data);
            $reservation = $this->repository->getReservation($id);
            $this->emailService->sendEmail(
                $reservation['email'],
                'Reservation received',
                'Your reservation has been received and is pending confirmation'
            );
            return $this->respondWithData(['ok' => $reservation], 201);
        } catch (\Exception $e) {
            return $this->respondWithData(['error' => $e->getMessage()], 409);
        }
    }
}
