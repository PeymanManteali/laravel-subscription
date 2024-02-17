<?php

namespace SubscriptionService\Models;

use App\Models\User;
use Packages\Subscription\Models\Package;
use Packages\Payment\Payable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, Payable, SoftDeletes;

    public $fillable = [
        'user_id',
        'package_id',
        'type',
        'price',
        'quantity',
        'description',
        'status',
        'expired_at'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
