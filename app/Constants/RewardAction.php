<?php

namespace App\Constants;

class RewardAction
{
    public const PAYMENT = 'Payment'; // When a customer pays a business from the app with the business's merchant code
    public const ONLINE_PURCHASE = 'Online Purchase'; // When a customer purchases from a business that has integrated with our 3rd party API. eg- an ecommerce store
    public const POS_PURCHASE = 'POS Purchase'; // When a customer uses any of the POS transaction type (withdraw or deposit)
    public const REFERRAL = 'Referral'; // When a customer refers another customer
    public const WALLET_FUNDED = 'Wallet Funded'; // When a customer credits their wallet
}
