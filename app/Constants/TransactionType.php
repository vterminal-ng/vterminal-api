<?php

namespace App\Constants;

class TransactionType
{
    public const VWITHDRAWAL = "withdrawal"; // The main POS withdrawal
    public const VDEPOSIT = "deposit"; // The main POS deposit into bank
    public const CREDIT_WALLET = "credit"; // deposit into or credit vterminal wallet
    public const PAYOUT = "payout"; // withdraw from vterminal wallet to your bank
    public const ADD_CARD = "add_card";
}
