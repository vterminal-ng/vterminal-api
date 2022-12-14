<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Code;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MerchantDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        $totalWalletBalance = 0;
        foreach (User::all() as $user) {
            $totalWalletBalance += $user->balance;
        }

        // Total Amount in wallets (Customer)
        $customerWalletsBalance = 0;
        foreach (User::where('role', 'customer')->get() as $user) {
            $customerWalletsBalance += $user->balance;
        }

        // Total Amount in wallets (Merchant)
        $merchantWalletsBalance = 0;
        foreach (User::where('role', 'merchant')->get() as $user) {
            $merchantWalletsBalance += $user->balance;
        }

        return view(
            'admin.dashboard',
            compact('usersCount', 'customersCount', 'merchantsCount', 'codesCount', 'activeCodesCount', 'pendingCodesCount', 'cancelledCodesCount', 'completedCodesCount', 'totalWalletBalance', 'customerWalletsBalance', 'merchantWalletsBalance')
        );
    }

    public function getUsers(Request $request)
    {
        // All Users
        $users = User::with('userDetail', 'merchantDetail')->latest()->get();

        if ($request->has('status')) {
            $status = $request->query('status');

            if ($status === 'active')
                // Get active users
                $users = User::where('updated_at', '>=', Carbon::today()->subMonth())->with('userDetail')->latest()->get();

            if ($status === 'inactive')
                // Get inactive users
                $users = User::whereBetween('updated_at', [Carbon::today()->subMonths(3), Carbon::today()->subMonth()])->with('userDetail')->latest()->get();

            if ($status === 'dormant')
                // Get dormant users
                $users = User::where('updated_at', '<', Carbon::today()->subMonths(3))->with('userDetail')->latest()->get();
        }

        return view('admin.users', compact('users'));
    }

    public function getUserDetails(User $user)
    {
        $user->load('userDetail', 'merchantDetail', 'customerCodes.customer', 'merchantCodes.merchant');
        return view('admin.user_details', compact('user'));
    }

    public function changeUserStatus(User $user)
    {
        $status = $user->is_active;
        $user->update(['is_active' => !$status]);

        return back()->with('success', 'User status updated succesfully!');
    }

    public function getCustomers(Request $request)
    {
        // All Customers
        $users = User::where('role', 'customer')->latest()->get();

        if ($request->has('status')) {
            $status = $request->query('status');

            if ($status === 'active')
                // Get active customers
                $users = User::where('role', 'customer')->where('updated_at', '>=', Carbon::today()->subMonth())->with('userDetail')->latest()->get();

            if ($status === 'inactive')
                // Get inactive customers
                $users = User::where('role', 'customer')->whereBetween('updated_at', [Carbon::today()->subMonths(3), Carbon::today()->subMonth()])->with('userDetail')->latest()->get();

            if ($status === 'dormant')
                // Get dormant customers
                $users = User::where('role', 'customer')->where('updated_at', '<', Carbon::today()->subMonths(3))->with('userDetail')->latest()->get();
        }

        return view('admin.customers', compact('users'));
    }

    public function getMerchants(Request $request)
    {
        // All Merchants
        $users = User::where('role', 'merchant')->with('userDetail')->latest()->get();

        if ($request->has('status')) {
            $status = $request->query('status');

            if ($status === 'active')
                // Get active merchants
                $users = User::where('role', 'merchant')->where('updated_at', '>=', Carbon::today()->subMonth())->with('userDetail')->latest()->get();

            if ($status === 'inactive')
                // Get inactive merchants
                $users = User::where('role', 'merchant')->whereBetween('updated_at', [Carbon::today()->subMonths(3), Carbon::today()->subMonth()])->with('userDetail')->latest()->get();

            if ($status === 'dormant')
                // Get dormant merchants
                $users = User::where('role', 'merchant')->where('updated_at', '<', Carbon::today()->subMonths(3))->with('userDetail')->latest()->get();
        }

        return view('admin.merchants', compact('users'));
    }
    public function getImage($id){
        $user = User::find($id);
        if($user) {
            return view('admin.merchant_address', compact('user'));
        }
        else {
            
            return response('id not found');
        }

       
    }
    public function verify($id)
    {
        $user = User::find($id);
        if($user) {
            $user->update([
                'address_verified_at' => true
            ]);
        }
       
        return redirect()->back()->with('success',' address verified!');
    }
}
