<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomerMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerProfile::with(['user', 'accounts']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('cif_number', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('cnic_passport', 'like', "%{$s}%");
            });
        }

        if ($request->filled('risk_rating')) {
            $query->where('risk_rating', $request->risk_rating);
        }

        $customers = $query->latest()->paginate(10);
        return view('cbs.customers.index', compact('customers'));
    }

    public function create()
    {
        $branches = Branch::where('status', 'active')->get();
        return view('cbs.customers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'salutation' => 'required|string',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'cnic_passport' => 'required|string|unique:customer_profiles,cnic_passport',
            'cnic_expiry_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'residential_address' => 'required|string',
            'city' => 'required|string',
            'occupation' => 'nullable|string',
            'monthly_income' => 'nullable|numeric',
            'is_pep' => 'nullable|boolean',
            'risk_rating' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::findOrFail($validated['branch_id']);
        $nextSeq = CustomerProfile::count() + 101;
        $cifNumber = 'CIF-' . $branch->code . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

        // 1. Create Portal User for Customer
        $user = User::create([
            'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
            'email' => $validated['email'],
            'password' => Hash::make('Bank@1234'), // Default temporary password
            'role' => 'customer',
            'branch_id' => $branch->id,
            'phone' => $validated['phone'],
            'transaction_pin' => Hash::make('1234'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        // 2. Create Customer Master Record
        $profile = CustomerProfile::create([
            'cif_number' => $cifNumber,
            'user_id' => $user->id,
            'salutation' => $validated['salutation'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'cnic_passport' => $validated['cnic_passport'],
            'cnic_expiry_date' => $validated['cnic_expiry_date'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'nationality' => 'Pakistani',
            'occupation' => $validated['occupation'],
            'source_of_income' => 'Employment / Business',
            'monthly_income' => $validated['monthly_income'] ?? 0.00,
            'residential_address' => $validated['residential_address'],
            'city' => $validated['city'],
            'country' => 'Pakistan',
            'is_pep' => $request->boolean('is_pep'),
            'risk_rating' => $validated['risk_rating'],
            'kyc_status' => 'verified',
            'biometric_verified' => true,
        ]);

        return redirect()->route('cbs.customers.show', $profile->id)->with('success', "Customer CIF {$cifNumber} onboarded successfully.");
    }

    public function show($id)
    {
        $customer = CustomerProfile::with(['user', 'accounts.product', 'accounts.branch', 'debitCards', 'loans'])->findOrFail($id);
        return view('cbs.customers.show', compact('customer'));
    }
}
