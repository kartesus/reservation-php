<?php

namespace App\Application\Actions\Reservation;

use Psr\Log\LoggerInterface;
use App\Application\Actions\Action;
use App\Infrastructure\Email\EmailService;
use App\Infrastructure\Persistence\Reservations\ReservationsRepository;

abstract class ReservationAction extends Action
{
    protected ReservationsRepository $repository;
    protected EmailService $emailService;

    public function __construct(LoggerInterface $logger, ReservationsRepository $repository, EmailService $emailService)
    {
        parent::__construct($logger);
        $this->repository = $repository;
        $this->emailService = $emailService;
    }
}
