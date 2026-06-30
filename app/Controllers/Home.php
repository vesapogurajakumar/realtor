<?php

declare(strict_types=1);

namespace App\Controllers;

use Config\Services;

class Home extends BaseController
{
    /**
     * Home page — hero, featured listings, stats, neighborhoods, testimonials, blog preview, lead capture.
     */
    public function index(): string
    {
        $properties = Services::property();
        $blog       = Services::blog();

        return view('home', [
            'title'           => 'Vesta | Luxury Real Estate & Homes for Sale',
            'metaDescription' => 'Discover luxury homes, waterfront villas, penthouses and estates. Vesta connects discerning buyers with the finest properties and expert real estate advisors.',
            'activeNav'       => 'home',
            'featured'        => $properties->featured(6),
            'neighborhoods'   => $properties->neighborhoods(),
            'cities'          => $properties->cities(),
            'types'           => $properties->types(),
            'posts'           => $blog->recent(3),
        ]);
    }

    /**
     * About page — company story, timeline, team, stats.
     */
    public function about(): string
    {
        $team = [
            ['name' => 'Sarah Mitchell', 'title' => 'Founder & Principal Broker', 'photo' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Daniel Reyes', 'title' => 'Waterfront Specialist', 'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Olivia Bennett', 'title' => 'Mountain & Estate Director', 'photo' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Marcus Chen', 'title' => 'Coastal Luxury Advisor', 'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Eleanor Hayes', 'title' => 'Manhattan Townhouse Expert', 'photo' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'James Whitfield', 'title' => 'Leasing & Rentals Lead', 'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80'],
        ];

        return view('about', [
            'title'           => 'About Vesta | Our Story & Team',
            'metaDescription' => 'Meet the Vesta team — a boutique luxury brokerage built on data, design and white-glove service. Fifteen years of closing the finest homes.',
            'activeNav'       => 'about',
            'team'            => $team,
        ]);
    }

    /**
     * Contact page (GET) — split form + map + agent cards.
     */
    public function contactPage(): string
    {
        return view('contact', [
            'title'           => 'Contact Vesta | Talk to a Luxury Advisor',
            'metaDescription' => 'Get in touch with the Vesta team. Whether you are buying, selling, renting or investing, our advisors are ready to help.',
            'activeNav'       => 'contact',
            'agents'          => Services::property()->all(),
        ]);
    }

    /**
     * Contact / lead form submission (POST, AJAX).
     */
    public function submitContact()
    {
        return $this->handleLead([
            'name'    => 'required|min_length[2]|max_length[120]',
            'email'   => 'required|valid_email|max_length[180]',
            'phone'   => 'permit_empty|numeric|exact_length[10]',
            'message' => 'permit_empty|max_length[2000]',
        ], 'lead', [
            'phone' => [
                'numeric'      => 'Mobile number must contain digits only.',
                'exact_length' => 'Please enter a valid 10-digit mobile number.',
            ],
            'email' => ['valid_email' => 'Please enter a valid email address.'],
        ]);
    }

    /**
     * Newsletter subscribe (POST, AJAX) — used by footer & exit-intent modal.
     */
    public function subscribe()
    {
        return $this->handleLead([
            'email' => 'required|valid_email|max_length[180]',
        ], 'subscriber');
    }

    /**
     * Shared lead/subscriber handler: honeypot, validation, persistence + email.
     * Returns JSON for AJAX. Persistence degrades gracefully when no DB is set.
     */
    private function handleLead(array $rules, string $kind, array $messages = [])
    {
        // Honeypot — silently accept bots without storing.
        if (trim((string) $this->request->getPost('company')) !== '') {
            return $this->jsonOk('Thank you!');
        }

        if (! $this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Please correct the highlighted fields.',
                'errors'  => $this->validator->getErrors(),
                'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $payload = [
            'name'           => (string) $this->request->getPost('name'),
            'email'          => (string) $this->request->getPost('email'),
            'phone'          => (string) $this->request->getPost('phone'),
            'interest'       => (string) $this->request->getPost('interest'),
            'message'        => (string) $this->request->getPost('message'),
            'preferred_time' => (string) $this->request->getPost('preferred_time'),
            'source'         => (string) ($this->request->getPost('source') ?: ucfirst($kind)),
            'ip'             => $this->request->getIPAddress(),
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $this->persistLead($kind, $payload);
        $this->sendNotification($kind, $payload);

        $message = $kind === 'subscriber'
            ? 'You\'re subscribed! Watch your inbox for our latest market report.'
            : 'Thank you! One of our advisors will reach out to you shortly.';

        return $this->jsonOk($message);
    }

    /**
     * Persist a lead. Tries the DB model (if a connection works), always keeps a
     * writable JSON backup so nothing is ever lost.
     */
    private function persistLead(string $kind, array $payload): void
    {
        // DB attempt (no-op until models/migrations & DB credentials exist).
        try {
            if ($kind === 'subscriber' && class_exists(\App\Models\SubscriberModel::class)) {
                (new \App\Models\SubscriberModel())->captureEmail($payload['email']);
            } elseif (class_exists(\App\Models\LeadModel::class)) {
                (new \App\Models\LeadModel())->insert($payload, false);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Lead DB persist failed: {msg}', ['msg' => $e->getMessage()]);
        }

        // Always append to the writable JSON backup.
        try {
            $dir = WRITEPATH . 'leads';
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $file    = $dir . DIRECTORY_SEPARATOR . $kind . 's.jsonl';
            file_put_contents($file, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            log_message('error', 'Lead JSON backup failed: {msg}', ['msg' => $e->getMessage()]);
        }
    }

    /**
     * Email the office. Wrapped so an unconfigured mailer never breaks submission.
     */
    private function sendNotification(string $kind, array $payload): void
    {
        try {
            $email = Services::email();
            $email->setTo('hello@vestarealty.com');
            $email->setFrom('no-reply@vestarealty.com', 'Vesta Website');
            $email->setSubject($kind === 'subscriber' ? 'New newsletter subscriber' : 'New website lead: ' . ($payload['source'] ?? ''));
            $body = '';
            foreach ($payload as $k => $v) {
                if ($v !== '') {
                    $body .= ucfirst(str_replace('_', ' ', $k)) . ': ' . $v . "\n";
                }
            }
            $email->setMessage($body);
            @$email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Lead email failed: {msg}', ['msg' => $e->getMessage()]);
        }
    }

    /**
     * Returns the current CSRF token (name + hash) as JSON.
     * Forms fetch this immediately before submitting to avoid stale-token 403s.
     */
    public function csrf()
    {
        return $this->response->setJSON(['name' => csrf_token(), 'hash' => csrf_hash()]);
    }

    private function jsonOk(string $message)
    {
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message,
            'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }
}
