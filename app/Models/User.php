<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'avatar',
        'role_id',
        'disabled_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'is_active',
        'has_active_subscription',
        'active_subscription',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function store()
    {
        return $this->belongsToMany(Store::class, 'user_stores');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getIsActiveAttribute()
    {
        return $this->disabled_at === null;
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions()
    {
        if ($this->role_id === 5) {
            $owner = $this->store()->first()->owners()->first();

            return Subscription::where('user_id', $owner->id)
                ->where('date_subscribed', '<', now())
                ->where('valid_to', '>', now())
                ->where('date_unsubscribed', null)
                ->latest()
                ->get();
        }

        return $this->subscriptions()
            ->where('date_subscribed', '<', now())
            ->where('valid_to', '>', now())
            ->where('date_unsubscribed', null)
            ->latest()
            ->get();
    }

    public function activeSubscription()
    {
        $subscriptions = $this->activeSubscriptions();

        if (!$subscriptions) {
            return null;
        }

        $subscriptions = $subscriptions->all();

        // Return the first active subscription
        foreach ($subscriptions as $subscription) {
            if ($subscription->status === 'Active') {
                return $subscription;
            }
        }


        return null;
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->activeSubscription();
    }

    public function getHasActiveSubscriptionAttribute()
    {
        return $this->getActiveSubscriptionAttribute() !== null;
    }

    public function invoices()
    {
        return $this->subscriptions()
            ->join('invoices', 'subscriptions.id', '=', 'invoices.subscription_id')
            ->select('invoices.*')
            ->where('subscriptions.user_id', $this->id)
            ->latest('invoices.created_at');
    }

    public function activeInvoices()
    {
        return $this->invoices()
            ->where('invoices.paid_at', null)
            ->where('invoices.due_at', '>', now());
    }
}
