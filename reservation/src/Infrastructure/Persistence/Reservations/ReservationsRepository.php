<?php

namespace App\Infrastructure\Persistence\Reservations;

use Psr\Container\ContainerInterface;

class ReservationsRepository
{
    private $db;

    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
    }

    public function getReservation(int $id)
    {
        $stmt = $this->db->prepare('SELECT r.id as reservation_id, r.reservation_date as date, r.status,
                                           r.table_type_id, t.name as table_type, c.id as customer_id, c.name, c.email
                                    FROM reservations r
                                    JOIN customers c ON c.id = r.customer_id
                                    JOIN table_types t ON t.id = r.table_type_id
                                    WHERE r.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAvailabilityForDate(string $date)
    {
        $stmt = $this->db->query('SELECT id, name, capacity FROM table_types');
        $tables = $stmt->fetchAll();

        $stmt = $this->db->prepare('SELECT table_type_id, COUNT(id) AS reservations 
                                    FROM reservations 
                                    WHERE reservation_date = ? 
                                    AND status IN ("pending", "confirmed")
                                    GROUP BY table_type_id');
        $stmt->execute([$date]);
        $reservations = $stmt->fetchAll();

        $result = [];
        foreach ($tables as $table) {
            $reserved_tables = 0;
            foreach ($reservations as $reservation)
                if ($reservation['table_type_id'] == $table['id'])
                    $reserved_tables = intval($reservation['reservations']);

            $result[$table['id']] = $table;
            $result[$table['id']]['availability'] = $table['capacity'] - $reserved_tables;
        }

        return $result;
    }

    public function makeReservation(array $data): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare('SELECT COUNT(id) AS reservations 
                                    FROM reservations 
                                    WHERE reservation_date = ? 
                                    AND table_type_id = ?
                                    AND status IN ("pending", "confirmed")');

        $stmt->execute([$data['reservation_date'], $data['table_type_id']]);
        $reservations = $stmt->fetch();

        $stmt = $this->db->prepare('SELECT capacity FROM table_types WHERE id = ?');
        $stmt->execute([$data['table_type_id']]);
        $table = $stmt->fetch();

        if ($reservations['reservations'] >= $table['capacity']) {
            $this->db->rollBack();
            throw new \Exception('Table is not available');
        }

        $stmt = $this->db->prepare('INSERT INTO reservations (customer_id, table_type_id, reservation_date) 
                                    VALUES (?, ?, ?)');
        $stmt->execute([
            $data['customer_id'],
            $data['table_type_id'],
            $data['reservation_date'],
        ]);

        $last_id = $this->db->lastInsertId();

        $this->db->commit();
        return $last_id;
    }

    public function confirmReservation(int $id)
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare('SELECT status FROM reservations WHERE id = ?');
        $stmt->execute([$id]);
        $reservation = $stmt->fetch();

        if ($reservation['status'] == 'cancelled') {
            $this->db->rollBack();
            throw new \Exception('Reservation cannot be confirmed');
        }

        $stmt = $this->db->prepare('UPDATE reservations SET status = "confirmed" WHERE id = ?');
        $stmt->execute([$id]);
        $this->db->commit();

        return $this->getReservation($id);
    }

    public function cancelReservation(int $id)
    {
        $stmt = $this->db->prepare('UPDATE reservations SET status = "cancelled" WHERE id = ?');
        $stmt->execute([$id]);
        return $this->getReservation($id);
    }

    public function addToWaitlist(array $data)
    {
        $stmt = $this->db->prepare('INSERT INTO reservations (customer_id, table_type_id, reservation_date, status)
                                    VALUES (?, ?, ?, "waitlist")');
        $stmt->execute([$data['customer_id'], $data['table_type_id'], $data['reservation_date']]);
        $id = $this->db->lastInsertId();
        return $this->getReservation($id);
    }

    public function promoteFromWaitlist(string $date, int $table_type_id)
    {
        $stmt = $this->db->prepare('SELECT id, customer_id, reservation_date, table_type_id
                                    FROM reservations
                                    WHERE reservation_date = ?
                                    AND table_type_id = ?
                                    AND status = "waitlist"
                                    ORDER BY id ASC
                                    LIMIT 1');

        $stmt->execute([$date, $table_type_id]);
        $reservation = $stmt->fetch();

        if (!$reservation)
            return null;

        $stmt = $this->db->prepare('UPDATE reservations SET status = "pending" WHERE id = ?');
        $stmt->execute([$reservation['id']]);
        return $this->getReservation($reservation['id']);
    }
}
