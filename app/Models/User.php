<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use App\Observers\UserObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role', 'avatar', 'birth_date', 'gender', 'region', 'city', 'profession',
        'bio', 'interests', 'profile_completed',
        'greencoin_balance', 'login_streak', 'last_login_at',
        'referred_by', 'referral_rewarded', 'referral_count',
        'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'birth_date' => 'date',
        'interests' => 'array',
        'two_factor_recovery_codes' => 'array',
        'is_banned' => 'boolean',
        'profile_completed' => 'boolean',
        'referral_rewarded' => 'boolean',
        'referral_count' => 'integer',
        'greencoin_balance' => 'integer',
        'login_streak' => 'integer',
        'role' => Role::class,
    ];

    // ── Scope helpers ──────────────────────────────────────────────────────

    public function scopeStaff($query)
    {
        return $query->whereIn('role', [Role::Admin->value, Role::Moderator->value]);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot(['progress', 'completed_at', 'certificate_code'])->withTimestamps();
    }

    public function dailyTasks(): HasMany
    {
        return $this->hasMany(DailyTask::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function forumTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GreenCoinTransaction::class);
    }

    public function ecoCompletions(): HasMany
    {
        return $this->hasMany(EcoTaskCompletion::class);
    }

    public function telegramUsers(): HasMany
    {
        return $this->hasMany(TelegramUser::class);
    }

    public function referralLink(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    public function businessProfile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')->withPivot('achieved_at')->withTimestamps();
    }

    public function mentorshipsAsMentor(): HasMany
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isStaff(): bool
    {
        return $this->role?->isStaff() ?? false;
    }

    public function isBanned(): bool
    {
        return $this->is_banned;
    }

    public function generateReferralCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function referralUrl(): string
    {
        return route('register', ['ref' => $this->referral_code]);
    }

    public function initials(): string
    {
        return Str::of($this->name)->trim()->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
    }
}
