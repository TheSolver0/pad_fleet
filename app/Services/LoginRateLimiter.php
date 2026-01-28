<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LoginRateLimiter
{
    /** Durées de blocage en secondes : 3 min, 5 min, 10 min, 30 min, 1h, 3h, 6h, 12h, 24h, suspension (24h) */
    private const LOCKOUT_DURATIONS = [180, 300, 600, 1800, 3600, 10800, 21600, 43200, 86400, 86400];

    private const MAX_ATTEMPTS_BEFORE_FIRST_LOCK = 5;

    private const KEY_ATTEMPTS = 'login_attempts_';
    private const KEY_LOCKED_UNTIL = 'login_locked_until_';
    private const TTL_ATTEMPTS = 86400; // 24h pour réinitialiser le compteur si plus d'échecs

    public function __construct(
        private string $keySuffix = ''
    ) {
        $this->keySuffix = $keySuffix ?: request()->ip() ?? 'unknown';
    }

    public static function forIp(string $ip): self
    {
        return new self($ip);
    }

    public function isLocked(): bool
    {
        $until = Cache::get(self::KEY_LOCKED_UNTIL . $this->keySuffix);
        return $until && (int) $until > time();
    }

    public function remainingLockSeconds(): int
    {
        $until = Cache::get(self::KEY_LOCKED_UNTIL . $this->keySuffix);
        if (!$until || (int) $until <= time()) {
            return 0;
        }
        return (int) $until - time();
    }

    public function recordFailedAttempt(): int
    {
        $keyAttempts = self::KEY_ATTEMPTS . $this->keySuffix;
        $attempts = (int) Cache::get($keyAttempts, 0) + 1;
        Cache::put($keyAttempts, $attempts, self::TTL_ATTEMPTS);

        $level = max(0, $attempts - self::MAX_ATTEMPTS_BEFORE_FIRST_LOCK);
        $durationIndex = min($level, count(self::LOCKOUT_DURATIONS) - 1);
        $duration = self::LOCKOUT_DURATIONS[$durationIndex];

        $keyLocked = self::KEY_LOCKED_UNTIL . $this->keySuffix;
        Cache::put($keyLocked, time() + $duration, $duration + 60);

        return $duration;
    }

    public function clear(): void
    {
        Cache::forget(self::KEY_ATTEMPTS . $this->keySuffix);
        Cache::forget(self::KEY_LOCKED_UNTIL . $this->keySuffix);
    }

    public function formatRemainingTime(): string
    {
        $sec = $this->remainingLockSeconds();
        if ($sec <= 0) {
            return '';
        }
        if ($sec < 60) {
            return $sec . ' seconde(s)';
        }
        if ($sec < 3600) {
            return (int) ceil($sec / 60) . ' minute(s)';
        }
        if ($sec < 86400) {
            return (int) ceil($sec / 3600) . ' heure(s)';
        }
        return (int) ceil($sec / 86400) . ' jour(s)';
    }
}
