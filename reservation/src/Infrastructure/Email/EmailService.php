<?php

namespace App\Infrastructure\Email;

use Psr\Container\ContainerInterface;

class EmailService
{
    private $db;

    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
    }

    public function sendEmail(string $to, string $subject, string $body)
    {
        $stmt = $this->db->prepare('INSERT INTO emails (recipient, subject, body) 
                                    VALUES (?, ?, ?)');

        $stmt->execute([$to, $subject, $body]);

        return true;
    }
}
