<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class SubscriberModel extends Model
{
    protected $table         = 'subscribers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields  = ['email', 'is_active', 'subscribed_at'];

    /**
     * Insert a new subscriber, or re-activate one that previously unsubscribed.
     * Idempotent on email (which is unique).
     */
    public function captureEmail(string $email): bool
    {
        $email    = strtolower(trim($email));
        $existing = $this->where('email', $email)->first();

        if ($existing !== null) {
            if ((int) ($existing['is_active'] ?? 0) !== 1) {
                $this->update($existing['id'], ['is_active' => 1]);
            }

            return true;
        }

        return (bool) $this->insert([
            'email'         => $email,
            'is_active'     => 1,
            'subscribed_at' => date('Y-m-d H:i:s'),
        ], false);
    }
}
