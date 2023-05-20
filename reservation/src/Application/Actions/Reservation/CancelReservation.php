<?php

namespace App\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class CancelReservation extends ReservationAction
{
    protected function action(): Response
    {
        try {
            $id = $this->resolveArg('id');
            $reservation = $this->repository->cancelReservation($id);
            $this->emailService->sendEmail(
                $reservation['email'],
                'Reservation canceled',
                'Your reservation has been canceled'
            );

            $promoted = $this->repository->promoteFromWaitlist($reservation['date'], $reservation['table_type_id']);

            if ($promoted) {
                $this->emailService->sendEmail(
                    $promoted['email'],
                    'Reservation pending',
                    'Your reservation has been promoted from the waitlist and is pending confirmation'
                );
            }
            return $this->respondWithData(['ok' => $reservation], 200);
        } catch (\Exception $e) {
            return $this->respondWithData(['error' => $e->getMessage()], 409);
        }
    }
}
