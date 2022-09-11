<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Code;

class DashboardController extends Controller
{
    public function displayDashboard()
    {   
        // Users
        $usersCount = User::count();
        $customersCount = User::where('role', 'customer')->count();
        $merchantsCount = User::where('role', 'merchant')->count();

        // Code transactions
        $codesCount = Code::count();
        $activeCodesCount = Code::where('status', 'active')->count();
        $pendingCodesCount = Code::where('status', 'pending')->count();
        $cancelledCodesCount = Code::where('status', 'cancelled')->count();
        $completedCodesCount = Code::where('status', 'completed')->count();

        // Total amount of code transactions
        $totalWalletBalance = DB::table('wallets')->sum('balance');
        // Total Amount in wallets (Customer)
        $customerWalletsBalance = 0;
        // Total Amount in wallets (Merchant)
        $merchantWalletsBalance = 0;

        return view(
            'admin.dashboard', 
            compact('usersCount', 'customersCount', 'merchantsCount', 'codesCount', 'activeCodesCount', 'pendingCodesCount', 'cancelledCodesCount', 'completedCodesCount', 'totalWalletBalance', 'customerWalletsBalance', 'merchantWalletsBalance')
        );
    }

    public function getUsers(Request $request)
    {   
        // All Users
        $users = User::latest()->with('userDetail')->get();

        if($request->has('status')){
            $status = $request->query('status');

            if($status === 'active')
                // Get active users
                $users = [];

            if($status === 'inactive')
                // Get inactive users
                $users = [];

            if($status === 'dormant')
                // Get dormant users
                $users = [];
        }

        return view('admin.user', compact('users'));
    }
}
