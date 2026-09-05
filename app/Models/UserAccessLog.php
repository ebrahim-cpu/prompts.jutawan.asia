<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class UserAccessLog
 *
 * Audit log recording authentication events (LOGIN / LOGOUT) with IP,
 * device heuristics, User-Agent, and referer headers.
 *
 * @package App\Models
 * @property int $id
 * @property int|null $user_id
 * @property string|null $user_name
 * @property string|null $user_email
 * @property string $event_type 'LOGIN' or 'LOGOUT'
 * @property string $ip_address
 * @property string|null $user_agent
 * @property string|null $url
 * @property string|null $method
 * @property string|null $referer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user Associated user relationship
 * @property-read array{label: string, bg: string, icon: string} $event_badge UI badge styling
 * @property-read string $browser_summary Human-readable browser and device string
 */
class UserAccessLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_access_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'event_type',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'referer',
    ];

    /**
     * Relationship to the user whose authentication triggered the event.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get visual badge styling attributes (label, background color, icon) for the event.
     *
     * @return array{label: string, bg: string, icon: string}
     */
    public function getEventBadgeAttribute(): array
    {
        $type = strtoupper($this->event_type ?? 'LOGIN');
        if ($type === 'LOGOUT') {
            return [
                'label' => 'Log Keluar',
                'bg'    => 'bg-red-500/10 border-red-500/20 text-red-400',
                'icon'  => '🚪'
            ];
        }
        return [
            'label' => 'Log Masuk Berjaya',
            'bg'    => 'bg-green-500/10 border-green-500/20 text-green-400',
            'icon'  => '🔑'
        ];
    }

    /**
     * Parse the raw HTTP User-Agent into a clean browser name and device classification.
     *
     * @return string E.g. "Chrome (Desktop)" or "Safari (Telefon Bimbit)"
     */
    public function getBrowserSummaryAttribute(): string
    {
        $agent = $this->user_agent ?? '';
        if (empty($agent)) {
            return 'Peranti Tidak Diketahui';
        }

        $platform = 'Desktop';
        if (preg_match('/mobile/i', $agent)) {
            $platform = 'Telefon Bimbit';
        } elseif (preg_match('/tablet|ipad/i', $agent)) {
            $platform = 'Tablet';
        }

        $browser = 'Pelayar Web';
        if (preg_match('/chrome/i', $agent) && !preg_match('/edg/i', $agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $agent) && !preg_match('/chrome/i', $agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox/i', $agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/edg/i', $agent)) {
            $browser = 'Edge';
        }

        return "{$browser} ({$platform})";
    }
}
