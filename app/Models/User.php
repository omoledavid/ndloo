<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Contracts\Enums\ReactionTypes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'age',
        'type',
        'wallet',
        'credits',
        'gender',
        'dob',
        'language',
        'avatar',
        'country_id',
        'is_admin',
        'username',
        'password',
        'pushNotice',
        'longitude',
        'latitude',
        'active',
        'status',
        'token',
        'is_online',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'pushNotice' => 'bool',
            'active' => 'bool',
            'is_admin' => 'bool',
            'is_online' => 'bool',
            'last_seen_at' => 'datetime',
        ];
    }

    public function wallet(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => $value / 100,
            set: fn(int $value) => $value * 100
        );
    }

    public function credits(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => $value / 100,
            set: fn(int $value) => $value * 100
        );
    }

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => asset($value),

        );
    }

    public function pushtokens(): HasMany
    {
        return $this->hasMany(AppToken::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProfileImage::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class);
    }
    public function mySubscriptions(): HasMany
    {
        return $this->HasMany(NdSubscription::class, 'user_id');
    }
    public function activeSubscription()
    {
        return $this->mySubscriptions()
            ->where(function ($query) {
                $now = now();
                $query->where(function ($q) use ($now) {
                    $q->where('starts_at', '<=', $now)
                      ->where('ends_at', '>=', $now);
                })
                ->orWhere(function ($q) {
                    $q->whereDate('starts_at', '=', now()->toDateString())
                      ->whereDate('ends_at', '=', now()->toDateString());
                });
            })
            ->latest();
    }

    public function boosts(): BelongsToMany
    {
        return $this->belongsToMany(BoostPlan::class)->withPivot('active', 'expires_on');
    }

    public function profile(): BelongsToMany
    {
        return $this->belongsToMany(ProfileInfo::class)->withPivot('content');
    }

    public function myLikes(): HasMany
    {
        return $this->hasMany(Reaction::class, 'actor')
            ->where('type', ReactionTypes::LIKE->value);
    }

    public function blockList(): HasMany|array
    {
        return $this->hasMany(Reaction::class, 'actor')->where([
            ['type', ReactionTypes::BLOCKED->value],
            ['actor', auth()->user()?->id],
        ]) ?? [];
    }
    public function myGift()
    {
        return $this->hasMany(UserGift::class);
    }
    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
    public function getAccessToken()
    {
        $token = PersonalAccessToken::where('tokenable_id', $this->id)
            ->where('tokenable_type', self::class)
            ->latest()
            ->first();

        return $token ? $token->token : null;
    }
    public function livestreams()
    {
        return $this->hasMany(Livestream::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function sentGifts()
    {
        return $this->hasMany(GiftTransaction::class, 'sender_id');
    }

    public function receivedGifts()
    {
        return $this->hasMany(GiftTransaction::class, 'receiver_id');
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }
}
