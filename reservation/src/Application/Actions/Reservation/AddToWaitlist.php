<?php

namespace App\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class AddToWaitlist extends ReservationAction
{
    protected function action(): Response
    {
        try {
            $data = $this->request->getParsedBody();
            $waitlist = $this->repository->addToWaitlist($data);
            $this->emailService->sendEmail(
                $waitlist['email'],
                'You\'re on the waitlist',
                'You\'re on the waitlist and will be notified if a table becomes available'
            );
            return $this->respondWithData(['ok' => $waitlist], 201);
        } catch (\Exception $e) {
            return $this->respondWithData(['error' => $e->getMessage()], 409);
        }
    }
}
