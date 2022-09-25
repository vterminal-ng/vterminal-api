<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
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
        $users = User::with('userDetail')->latest()->get();

        if($request->has('status')){
            $status = $request->query('status');

            if($status === 'active')
                // Get active users
                $users = User::where('updated_at', '>=', Carbon::today()->subMonth())->with('userDetail')->latest()->get();

            if($status === 'inactive')
                // Get inactive users
                $users = User::whereBetween('updated_at', [Carbon::today()->subMonths(3), Carbon::today()->subMonth()])->with('userDetail')->latest()->get();

            if($status === 'dormant')
                // Get dormant users
                $users = User::where('updated_at', '<', Carbon::today()->subMonths(3))->with('userDetail')->latest()->get();
        }

        return view('admin.users', compact('users'));
    }

    public function getCustomers(Request $request)
    {   
        // All Customers
        $users = User::where('role', 'customer')->with('userDetail')->latest()->get();

        if($request->has('status')){
            $status = $request->query('status');

            if($status === 'active')
                // Get active customers
                $users = User::where('role', 'customer')->where('updated_at', '>=', Carbon::today()->subMonth())->with('userDetail')->latest()->get();

            if($status === 'inactive')
                // Get inactive customers
                $users = User::where('role', 'customer')->whereBetween('updated_at', [Carbon::today()->subMonths(3), Carbon::today()->subMonth()])->with('userDetail')->latest()->get();

            if($status === 'dormant')
                // Get dormant customers
                $users = User::where('role', 'customer')->where('updated_at', '<', Carbon::today()->subMonths(3))->with('userDetail')->latest()->get();
        }

        return view('admin.customers', compact('users'));
    }

    public function getMerchants(Request $request)
    {   
        // All Merchants
        $users = User::where('role', 'merchant')->with('userDetail')->latest()->get();

        if($request->has('status')){
            $status = $request->query('status');

            if($status === 'active')
                // Get active merchants
                $users = User::where('role', 'merchant')->where('updated_at', '>=', Carbon::today()->subMonth())->with('userDetail')->latest()->get();

            if($status === 'inactive')
                // Get inactive merchants
                $users = User::where('role', 'merchant')->whereBetween('updated_at', [Carbon::today()->subMonths(3), Carbon::today()->subMonth()])->with('userDetail')->latest()->get();

            if($status === 'dormant')
                // Get dormant merchants
                $users = User::where('role', 'merchant')->where('updated_at', '<', Carbon::today()->subMonths(3))->with('userDetail')->latest()->get();
        }

        return view('admin.merchants', compact('users'));
    }
}
