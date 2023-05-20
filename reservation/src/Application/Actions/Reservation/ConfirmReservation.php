<?php

namespace App\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class ConfirmReservation extends ReservationAction
{
    protected function action(): Response
    {
        try {
            $id = $this->resolveArg('id');
            $reservation = $this->repository->confirmReservation($id);
            $this->emailService->sendEmail(
                $reservation['email'],
                'Reservation confirmed',
                'Your reservation has been confirmed'
            );
            return $this->respondWithData(['ok' => $reservation], 201);
        } catch (\Exception $e) {
            return $this->respondWithData(['error' => $e->getMessage()], 409);
        }
    }
}
