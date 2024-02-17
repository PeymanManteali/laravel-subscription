<?php

namespace SubscriptionService;

use SubscriptionService\Models\Package;
use SubscriptionService\Models\Subscription;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasSubscription //use this trait in user class
{
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Subscribe a user to a new plan
    public function subscribe(Package $package=null, $package_id=null): Subscription
    {
        if($package==null and $package_id) $package = Package::find($package_id);
        $subscription = new Subscription();
        //Initialization data
        $subscription->user_id = $this->id;
        $subscription->type = $package->type;
        $subscription->price = $package->price;
        $subscription->status = 'created';
        $subscription->quantity = $package->quantity;
        $subscription->package()->associate($package);
        $subscription->save();
        return $subscription;
    }

    // Determine if the user is subscribed to the given plan
    public function subscribed($packageType): bool
    {
        if ($this->getActiveSubscriptions($packageType)) {
            return true;
        }
        return false;
    }


    // Cancel the user's subscription at the end of the billing period

    /**
     * @param $subscription_id
     * @return bool
     * @throws Exception
     */
    public function cancelSubscription($subscription_id): bool
    {
        $subscription = $this->subscriptions()->where('id', $subscription_id)->first();
        if (!$subscription) {
            throw new Exception("SubscriptionService: subscription with id $subscription_id is not exist for this user");
        }
        $subscription->update(['status' => 'canceled']);
        return true;
    }


    // Subscribe and pay method
    public function subscribeAndPay($packageId, $paymentMethod): bool
    {
        $subscription = $this->subscribe($packageId);
        return $subscription->executePayment($paymentMethod);
    }

    public function getActiveSubscriptions($packageType): Collection
    {
        return $this->subscriptions()
            ->where('type', $packageType)->where('status', 'verified')
            ->where('expired_at', '>', now())
            ->get();
    }
}
