<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\AccountProduct;
use App\Models\CustomerProfile;
use App\Models\BankAccount;
use App\Models\DebitCard;
use App\Models\ChequeBook;
use App\Models\ChequeLeaf;
use App\Models\Biller;
use App\Models\BillPayment;
use App\Models\Beneficiary;
use App\Models\LoanFinancing;
use App\Models\GeneralLedgerEntry;
use App\Models\Transaction;
use App\Models\SecurityAuditLog;
use App\Services\IbanGeneratorService;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed Khazana Core Banking System (Khazana-CBS).
     */
    public function run(): void
    {
        // 1. Chart of Accounts (COA)
        $glAccounts = [
            // Assets (1000 - 1999)
            ['account_code' => '1010', 'account_name' => 'Cash in Vaults & Till Cash', 'account_type' => 'asset', 'sub_category' => 'cash_and_equivalents', 'normal_balance' => 'debit', 'current_balance' => 45000000.00],
            ['account_code' => '1020', 'account_name' => 'Statutory Reserves with State Bank of Pakistan', 'account_type' => 'asset', 'sub_category' => 'central_bank_reserves', 'normal_balance' => 'debit', 'current_balance' => 120000000.00],
            ['account_code' => '1030', 'account_name' => '1LINK & Raast Interbank Clearing Account', 'account_type' => 'asset', 'sub_category' => 'clearing_settlement', 'normal_balance' => 'debit', 'current_balance' => 25000000.00],
            ['account_code' => '1040', 'account_name' => 'Consumer & Auto Financing Portfolio', 'account_type' => 'asset', 'sub_category' => 'loans_and_advances', 'normal_balance' => 'debit', 'current_balance' => 85000000.00],

            // Liabilities (2000 - 2999)
            ['account_code' => '2010', 'account_name' => 'Customer Demand Deposits - Current Accounts', 'account_type' => 'liability', 'sub_category' => 'customer_deposits', 'normal_balance' => 'credit', 'current_balance' => 110000000.00],
            ['account_code' => '2020', 'account_name' => 'Customer PLS Savings Deposit Accounts', 'account_type' => 'liability', 'sub_category' => 'customer_deposits', 'normal_balance' => 'credit', 'current_balance' => 95000000.00],
            ['account_code' => '2030', 'account_name' => 'Term Deposit Receipts (TDR Fixed Deposits)', 'account_type' => 'liability', 'sub_category' => 'time_deposits', 'normal_balance' => 'credit', 'current_balance' => 40000000.00],
            ['account_code' => '2040', 'account_name' => '1BILL Utility Collections Clearing Payable', 'account_type' => 'liability', 'sub_category' => 'payables', 'normal_balance' => 'credit', 'current_balance' => 1500000.00],

            // Equity (3000 - 3999)
            ['account_code' => '3010', 'account_name' => 'Paid-up Capital (SBP MCR Authorized)', 'account_type' => 'equity', 'sub_category' => 'capital', 'normal_balance' => 'credit', 'current_balance' => 25000000.00],
            ['account_code' => '3020', 'account_name' => 'Statutory Bank Reserve Fund', 'account_type' => 'equity', 'sub_category' => 'reserves', 'normal_balance' => 'credit', 'current_balance' => 3500000.00],

            // Revenue (4000 - 4999)
            ['account_code' => '4010', 'account_name' => 'Commission, IBFT & Transaction Fee Income', 'account_type' => 'revenue', 'sub_category' => 'fee_income', 'normal_balance' => 'credit', 'current_balance' => 3200000.00],
            ['account_code' => '4020', 'account_name' => 'Markup & Financing Profit Earned', 'account_type' => 'revenue', 'sub_category' => 'markup_income', 'normal_balance' => 'credit', 'current_balance' => 6800000.00],

            // Expenses (5000 - 5999)
            ['account_code' => '5010', 'account_name' => 'PLS Savings Deposit Profit Expensed', 'account_type' => 'expense', 'sub_category' => 'interest_expense', 'normal_balance' => 'debit', 'current_balance' => 2800000.00],
            ['account_code' => '5020', 'account_name' => 'Branch Operating & Infrastructure Costs', 'account_type' => 'expense', 'sub_category' => 'administrative', 'normal_balance' => 'debit', 'current_balance' => 4200000.00],
        ];

        foreach ($glAccounts as $gl) {
            ChartOfAccount::create($gl);
        }

        // 2. Branches
        $khiBranch = Branch::create([
            'code' => '0010',
            'name' => 'Karachi Main Corporate Branch',
            'city' => 'Karachi',
            'province' => 'Sindh',
            'address' => 'Khazana Tower, I.I. Chundrigar Road, Financial District',
            'phone' => '+92-21-32450000',
            'email' => 'khi.main@khazanabank.pk',
            'swift_bic' => 'KHZNPKKA010',
            'routing_number' => '010001',
            'is_head_office' => true,
            'vault_cash_limit' => 50000000.00,
            'current_vault_cash' => 22500000.00,
            'status' => 'active',
        ]);

        $lhrBranch = Branch::create([
            'code' => '0020',
            'name' => 'Lahore Mall Road Branch',
            'city' => 'Lahore',
            'province' => 'Punjab',
            'address' => '48 The Mall, Near High Court, Lahore',
            'phone' => '+92-42-37210000',
            'email' => 'lhr.mall@khazanabank.pk',
            'swift_bic' => 'KHZNPKKA020',
            'routing_number' => '020001',
            'is_head_office' => false,
            'vault_cash_limit' => 30000000.00,
            'current_vault_cash' => 14000000.00,
            'status' => 'active',
        ]);

        $isbBranch = Branch::create([
            'code' => '0030',
            'name' => 'Islamabad Blue Area Branch',
            'city' => 'Islamabad',
            'province' => 'Federal Capital',
            'address' => 'Plot 14, Jinnah Avenue, Blue Area, Islamabad',
            'phone' => '+92-51-2800000',
            'email' => 'isb.blue@khazanabank.pk',
            'swift_bic' => 'KHZNPKKA030',
            'routing_number' => '030001',
            'is_head_office' => false,
            'vault_cash_limit' => 20000000.00,
            'current_vault_cash' => 8500000.00,
            'status' => 'active',
        ]);

        // 3. Account Products
        $caaProduct = AccountProduct::create([
            'code' => 'CAA',
            'name' => 'Khazana Asaan Current Account',
            'category' => 'current',
            'currency' => 'PKR',
            'interest_rate' => 0.00,
            'minimum_balance' => 100.00,
            'daily_withdrawal_limit' => 500000.00,
            'description' => 'Chequebook, PayPak debit card, digital banking, zero monthly maintenance fees.',
            'is_active' => true,
        ]);

        $plsProduct = AccountProduct::create([
            'code' => 'PLS',
            'name' => 'Khazana Munafa PLS Savings Account',
            'category' => 'savings',
            'currency' => 'PKR',
            'interest_rate' => 11.50,
            'minimum_balance' => 5000.00,
            'daily_withdrawal_limit' => 1000000.00,
            'description' => 'Profit calculated on daily balance, credited monthly in compliance with SBP guidelines.',
            'is_active' => true,
        ]);

        $tdrProduct = AccountProduct::create([
            'code' => 'TDR',
            'name' => 'Khazana Mahana Amdan Term Deposit (1 Year)',
            'category' => 'tdr',
            'currency' => 'PKR',
            'interest_rate' => 14.25,
            'minimum_balance' => 100000.00,
            'daily_withdrawal_limit' => 0.00,
            'tenor_months' => 12,
            'early_withdrawal_penalty' => 2.00,
            'description' => 'High yield guaranteed return term deposit with monthly profit distribution.',
            'is_active' => true,
        ]);

        $fcyProduct = AccountProduct::create([
            'code' => 'FCY',
            'name' => 'Khazana Foreign Currency USD Account',
            'category' => 'foreign_currency',
            'currency' => 'USD',
            'interest_rate' => 3.50,
            'minimum_balance' => 500.00,
            'daily_withdrawal_limit' => 10000.00,
            'description' => 'SBP FE-25 compliant foreign currency account with cross-border SWIFT wire capability.',
            'is_active' => true,
        ]);

        // 4. Utility Billers (1BILL ecosystem)
        $billers = [
            ['code' => 'KELECTRIC', 'name' => 'K-Electric Limited', 'category' => 'electricity', 'consumer_number_label' => 'Account Number (13 Digits)', 'consumer_number_length' => 13, 'icon' => 'bx-bulb'],
            ['code' => 'LESCO', 'name' => 'Lahore Electric Supply Company (LESCO)', 'category' => 'electricity', 'consumer_number_label' => 'Reference Number (14 Digits)', 'consumer_number_length' => 14, 'icon' => 'bx-zap'],
            ['code' => 'SSGC', 'name' => 'Sui Southern Gas Company Limited', 'category' => 'gas', 'consumer_number_label' => 'Customer Number (10 Digits)', 'consumer_number_length' => 10, 'icon' => 'bx-flame'],
            ['code' => 'SNGPL', 'name' => 'Sui Northern Gas Pipelines Ltd', 'category' => 'gas', 'consumer_number_label' => 'Consumer ID (11 Digits)', 'consumer_number_length' => 11, 'icon' => 'bx-flame'],
            ['code' => 'PTCL', 'name' => 'PTCL Landline & EVO Broadband', 'category' => 'telecom', 'consumer_number_label' => 'Account ID / Phone with Area Code', 'consumer_number_length' => 10, 'icon' => 'bx-phone-call'],
            ['code' => 'FBR_TAX', 'name' => 'Federal Board of Revenue (PSID Tax)', 'category' => 'government_tax', 'consumer_number_label' => 'PSID 17-Digit Number', 'consumer_number_length' => 17, 'icon' => 'bx-receipt'],
        ];

        foreach ($billers as $b) {
            Biller::create($b);
        }

        // 5. Staff Users
        $defaultPassword = Hash::make('Bank@1234');

        $superAdmin = User::create([
            'name' => 'Syed Mujtaba Arshad',
            'email' => 'admin@khazanabank.pk',
            'password' => $defaultPassword,
            'role' => 'super_admin',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-300-1122334',
            'transaction_pin' => Hash::make('1234'),
            'two_factor_enabled' => true,
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $branchManager = User::create([
            'name' => 'Kamran Hashmi',
            'email' => 'manager.khi@khazanabank.pk',
            'password' => $defaultPassword,
            'role' => 'branch_manager',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-300-2233445',
            'transaction_pin' => Hash::make('1234'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $makerOfficer = User::create([
            'name' => 'Zubair Qureshi',
            'email' => 'maker.khi@khazanabank.pk',
            'password' => $defaultPassword,
            'role' => 'maker_officer',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-300-3344556',
            'transaction_pin' => Hash::make('1234'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $teller = User::create([
            'name' => 'Ayesha Siddiqui',
            'email' => 'teller.khi@khazanabank.pk',
            'password' => $defaultPassword,
            'role' => 'teller',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-300-4455667',
            'transaction_pin' => Hash::make('1234'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $complianceOfficer = User::create([
            'name' => 'Waqar Ahmed Mir',
            'email' => 'compliance@khazanabank.pk',
            'password' => $defaultPassword,
            'role' => 'compliance_officer',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-300-5566778',
            'transaction_pin' => Hash::make('1234'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        // 6. Customers & Bank Accounts
        // Customer 1: Tariq Mehmood (General Businessman)
        $user1 = User::create([
            'name' => 'Tariq Mehmood',
            'email' => 'tariq.mehmood@example.com',
            'password' => $defaultPassword,
            'role' => 'customer',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-321-8765432',
            'transaction_pin' => Hash::make('2580'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $profile1 = CustomerProfile::create([
            'cif_number' => 'CIF-0010-00101',
            'user_id' => $user1->id,
            'salutation' => 'Mr.',
            'first_name' => 'Tariq',
            'last_name' => 'Mehmood',
            'father_or_husband_name' => 'Muhammad Mehmood',
            'mother_maiden_name' => 'Rashida Begum',
            'cnic_passport' => '42101-1234567-1',
            'cnic_expiry_date' => Carbon::now()->addYears(5),
            'date_of_birth' => '1984-06-15',
            'gender' => 'male',
            'nationality' => 'Pakistani',
            'occupation' => 'Textile Trader & Exporter',
            'source_of_income' => 'Business Profit',
            'monthly_income' => 450000.00,
            'residential_address' => 'House 142-B, Block 4, Clifton',
            'city' => 'Karachi',
            'country' => 'Pakistan',
            'is_pep' => false,
            'risk_rating' => 'low',
            'kyc_status' => 'verified',
            'biometric_verified' => true,
        ]);

        $accNo1 = IbanGeneratorService::generateAccountNumber('0010', 100201);
        $iban1 = IbanGeneratorService::generateIban('0010', $accNo1);

        $acc1 = BankAccount::create([
            'account_number' => $accNo1,
            'iban' => $iban1,
            'customer_profile_id' => $profile1->id,
            'branch_id' => $khiBranch->id,
            'account_product_id' => $caaProduct->id,
            'account_title' => 'Tariq Mehmood - Business Current',
            'currency' => 'PKR',
            'available_balance' => 1850000.00,
            'ledger_balance' => 1850000.00,
            'status' => 'active',
            'opened_at' => Carbon::now()->subMonths(8),
        ]);

        DebitCard::create([
            'bank_account_id' => $acc1->id,
            'customer_profile_id' => $profile1->id,
            'card_number_masked' => '5241 89** **** 4892',
            'card_token' => 'CRD-TOKEN-' . strtoupper(Str::random(12)),
            'card_type' => 'visa_debit',
            'embossed_name' => 'TARIQ MEHMOOD',
            'expiry_month' => '09',
            'expiry_year' => '2029',
            'cvv_hash' => Hash::make('892'),
            'pin_hash' => Hash::make('2580'),
            'status' => 'active',
            'is_ecommerce_enabled' => true,
            'is_international_enabled' => true,
            'daily_atm_limit' => 100000.00,
            'daily_pos_limit' => 250000.00,
            'daily_ecom_limit' => 150000.00,
            'issued_at' => Carbon::now()->subMonths(7),
        ]);

        $book1 = ChequeBook::create([
            'bank_account_id' => $acc1->id,
            'book_number' => 'CHQ-0010-9821',
            'start_leaf' => '00982101',
            'end_leaf' => '00982125',
            'total_leaves' => 25,
            'status' => 'active',
            'issued_at' => Carbon::now()->subMonths(6),
        ]);

        for ($leaf = 1; $leaf <= 5; $leaf++) {
            ChequeLeaf::create([
                'cheque_book_id' => $book1->id,
                'bank_account_id' => $acc1->id,
                'leaf_number' => '0098210' . $leaf,
                'status' => 'unused',
            ]);
        }

        // Customer 2: Syeda Fatima Zahra (Professional & Saver)
        $user2 = User::create([
            'name' => 'Syeda Fatima Zahra',
            'email' => 'fatima.z@example.com',
            'password' => $defaultPassword,
            'role' => 'customer',
            'branch_id' => $lhrBranch->id,
            'phone' => '+92-333-5566778',
            'transaction_pin' => Hash::make('4321'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $profile2 = CustomerProfile::create([
            'cif_number' => 'CIF-0020-00102',
            'user_id' => $user2->id,
            'salutation' => 'Ms.',
            'first_name' => 'Syeda Fatima',
            'last_name' => 'Zahra',
            'father_or_husband_name' => 'Syed Ali Raza',
            'mother_maiden_name' => 'Tahira Batool',
            'cnic_passport' => '42201-9876543-2',
            'cnic_expiry_date' => Carbon::now()->addYears(7),
            'date_of_birth' => '1992-11-20',
            'gender' => 'female',
            'nationality' => 'Pakistani',
            'occupation' => 'Senior Data Scientist',
            'source_of_income' => 'Salary',
            'monthly_income' => 380000.00,
            'residential_address' => 'Apartment 402, Royal Heights, Gulberg III',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'is_pep' => false,
            'risk_rating' => 'low',
            'kyc_status' => 'verified',
            'biometric_verified' => true,
        ]);

        $accNo2 = IbanGeneratorService::generateAccountNumber('0020', 100202);
        $iban2 = IbanGeneratorService::generateIban('0020', $accNo2);

        $acc2 = BankAccount::create([
            'account_number' => $accNo2,
            'iban' => $iban2,
            'customer_profile_id' => $profile2->id,
            'branch_id' => $lhrBranch->id,
            'account_product_id' => $plsProduct->id,
            'account_title' => 'Syeda Fatima Zahra - Munafa Savings',
            'currency' => 'PKR',
            'available_balance' => 920000.00,
            'ledger_balance' => 920000.00,
            'accrued_profit' => 3450.00,
            'status' => 'active',
            'opened_at' => Carbon::now()->subMonths(10),
        ]);

        DebitCard::create([
            'bank_account_id' => $acc2->id,
            'customer_profile_id' => $profile2->id,
            'card_number_masked' => '6048 20** **** 1190',
            'card_token' => 'CRD-TOKEN-' . strtoupper(Str::random(12)),
            'card_type' => 'paypak',
            'embossed_name' => 'SYEDA FATIMA ZAHRA',
            'expiry_month' => '11',
            'expiry_year' => '2028',
            'cvv_hash' => Hash::make('334'),
            'pin_hash' => Hash::make('4321'),
            'status' => 'active',
            'is_ecommerce_enabled' => true,
            'daily_atm_limit' => 50000.00,
            'daily_pos_limit' => 100000.00,
            'daily_ecom_limit' => 50000.00,
            'issued_at' => Carbon::now()->subMonths(9),
        ]);

        // Customer 3: Malik Aslam (Politically Exposed Person - High Net Worth PEP)
        $user3 = User::create([
            'name' => 'Senator Malik Aslam',
            'email' => 'malik.aslam@example.com',
            'password' => $defaultPassword,
            'role' => 'customer',
            'branch_id' => $isbBranch->id,
            'phone' => '+92-300-8889990',
            'transaction_pin' => Hash::make('9900'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $profile3 = CustomerProfile::create([
            'cif_number' => 'CIF-0030-00103',
            'user_id' => $user3->id,
            'salutation' => 'Senator',
            'first_name' => 'Malik',
            'last_name' => 'Aslam',
            'father_or_husband_name' => 'Malik Khuda Bux',
            'mother_maiden_name' => 'Farzana Bibi',
            'cnic_passport' => '61101-3456789-3',
            'cnic_expiry_date' => Carbon::now()->addYears(3),
            'date_of_birth' => '1968-03-10',
            'gender' => 'male',
            'nationality' => 'Pakistani',
            'occupation' => 'Member of Parliament / Industrialist',
            'source_of_income' => 'Agriculture & Corporate Dividends',
            'monthly_income' => 2500000.00,
            'residential_address' => 'Villa 9, Sector F-6/2, Embassy Road',
            'city' => 'Islamabad',
            'country' => 'Pakistan',
            'is_pep' => true,
            'risk_rating' => 'pep',
            'kyc_status' => 'verified',
            'biometric_verified' => true,
            'notes' => 'Mandatory SBP Enhanced Due Diligence (EDD) required for all transactions > 1M PKR.',
        ]);

        $accNo3 = IbanGeneratorService::generateAccountNumber('0030', 100203);
        $iban3 = IbanGeneratorService::generateIban('0030', $accNo3);

        $acc3 = BankAccount::create([
            'account_number' => $accNo3,
            'iban' => $iban3,
            'customer_profile_id' => $profile3->id,
            'branch_id' => $isbBranch->id,
            'account_product_id' => $caaProduct->id,
            'account_title' => 'Malik Aslam - High Net Worth Corporate',
            'currency' => 'PKR',
            'available_balance' => 14800000.00,
            'ledger_balance' => 14800000.00,
            'status' => 'active',
            'opened_at' => Carbon::now()->subMonths(18),
        ]);

        // Customer 4: Bilal Farooq (Digital Tech Professional)
        $user4 = User::create([
            'name' => 'Bilal Farooq',
            'email' => 'bilal.farooq@example.com',
            'password' => $defaultPassword,
            'role' => 'customer',
            'branch_id' => $khiBranch->id,
            'phone' => '+92-315-7654321',
            'transaction_pin' => Hash::make('1122'),
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]);

        $profile4 = CustomerProfile::create([
            'cif_number' => 'CIF-0010-00104',
            'user_id' => $user4->id,
            'salutation' => 'Mr.',
            'first_name' => 'Bilal',
            'last_name' => 'Farooq',
            'father_or_husband_name' => 'Muhammad Farooq',
            'mother_maiden_name' => 'Shamim Akhtar',
            'cnic_passport' => '35202-5432109-5',
            'cnic_expiry_date' => Carbon::now()->addYears(6),
            'date_of_birth' => '1996-08-25',
            'gender' => 'male',
            'nationality' => 'Pakistani',
            'occupation' => 'Fintech Software Architect',
            'source_of_income' => 'Foreign Consultancy & Salary',
            'monthly_income' => 520000.00,
            'residential_address' => 'Flat 12-C, Creek Vistas, Phase VIII DHA',
            'city' => 'Karachi',
            'country' => 'Pakistan',
            'is_pep' => false,
            'risk_rating' => 'low',
            'kyc_status' => 'verified',
            'biometric_verified' => true,
        ]);

        $accNo4 = IbanGeneratorService::generateAccountNumber('0010', 100204);
        $iban4 = IbanGeneratorService::generateIban('0010', $accNo4);

        $acc4 = BankAccount::create([
            'account_number' => $accNo4,
            'iban' => $iban4,
            'customer_profile_id' => $profile4->id,
            'branch_id' => $khiBranch->id,
            'account_product_id' => $plsProduct->id,
            'account_title' => 'Bilal Farooq - Digital Premier Account',
            'currency' => 'PKR',
            'available_balance' => 2450000.00,
            'ledger_balance' => 2450000.00,
            'accrued_profit' => 8420.00,
            'status' => 'active',
            'opened_at' => Carbon::now()->subMonths(12),
        ]);

        DebitCard::create([
            'bank_account_id' => $acc4->id,
            'customer_profile_id' => $profile4->id,
            'card_number_masked' => '4111 22** **** 9012',
            'card_token' => 'CRD-TOKEN-' . strtoupper(Str::random(12)),
            'card_type' => 'visa_debit',
            'embossed_name' => 'BILAL FAROOQ',
            'expiry_month' => '05',
            'expiry_year' => '2030',
            'cvv_hash' => Hash::make('512'),
            'pin_hash' => Hash::make('1122'),
            'status' => 'active',
            'is_ecommerce_enabled' => true,
            'is_international_enabled' => true,
            'daily_atm_limit' => 100000.00,
            'daily_pos_limit' => 300000.00,
            'daily_ecom_limit' => 200000.00,
            'issued_at' => Carbon::now()->subMonths(11),
        ]);

        // Beneficiaries for Bilal Farooq
        Beneficiary::create([
            'user_id' => $user4->id,
            'nick_name' => 'Tariq Bhai (Textiles)',
            'beneficiary_name' => 'Tariq Mehmood',
            'transfer_type' => 'internal',
            'bank_name' => 'Khazana Bank',
            'account_or_iban' => $iban1,
            'email' => 'tariq.mehmood@example.com',
            'phone' => '+92-321-8765432',
            'is_verified' => true,
        ]);

        Beneficiary::create([
            'user_id' => $user4->id,
            'nick_name' => 'HBL Savings (Personal)',
            'beneficiary_name' => 'Bilal Farooq',
            'transfer_type' => '1link_ibft',
            'bank_name' => 'Habib Bank Limited (HBL)',
            'bank_code' => 'HABB001',
            'account_or_iban' => 'PK45HABB0001009876543210',
            'phone' => '+92-315-7654321',
            'is_verified' => true,
        ]);

        Beneficiary::create([
            'user_id' => $user4->id,
            'nick_name' => 'Faizan Raast Pay',
            'beneficiary_name' => 'Muhammad Faizan',
            'transfer_type' => 'raast_p2p',
            'bank_name' => 'Meezan Bank Limited',
            'bank_code' => 'MEZN001',
            'account_or_iban' => '+923001234567',
            'raast_id' => '+923001234567',
            'phone' => '+92-300-1234567',
            'is_verified' => true,
        ]);

        // Auto Financing for Tariq Mehmood
        LoanFinancing::create([
            'loan_reference' => 'LNF-2025-0019',
            'customer_profile_id' => $profile1->id,
            'bank_account_id' => $acc1->id,
            'product_type' => 'auto_loan',
            'principal_amount' => 4500000.00,
            'markup_rate' => 17.50,
            'tenor_months' => 36,
            'monthly_installment' => 161500.00,
            'total_payable' => 5814000.00,
            'remaining_balance' => 3876000.00,
            'installments_paid' => 12,
            'status' => 'active',
            'disbursed_date' => Carbon::now()->subMonths(12)->toDateString(),
            'maturity_date' => Carbon::now()->addMonths(24)->toDateString(),
        ]);

        // Historical Transactions for Demo
        Transaction::create([
            'transaction_reference' => 'DEP-20261020-009182',
            'source_account_id' => null,
            'destination_account_id' => $acc4->id,
            'branch_id' => $khiBranch->id,
            'maker_id' => $teller->id,
            'checker_id' => $branchManager->id,
            'transaction_type' => 'cash_deposit',
            'channel' => 'teller',
            'currency' => 'PKR',
            'amount' => 500000.00,
            'fee' => 0.00,
            'tax' => 0.00,
            'net_amount' => 500000.00,
            'narration' => 'Cash Deposit Counter Slip #49021',
            'status' => 'posted',
            'is_aml_flagged' => false,
            'posted_at' => Carbon::now()->subDays(11),
            'authorized_at' => Carbon::now()->subDays(11),
        ]);

        Transaction::create([
            'transaction_reference' => 'TRF-20261025-004312',
            'source_account_id' => $acc4->id,
            'destination_account_id' => $acc1->id,
            'branch_id' => $khiBranch->id,
            'maker_id' => $user4->id,
            'transaction_type' => 'internal_transfer',
            'channel' => 'digital',
            'currency' => 'PKR',
            'amount' => 120000.00,
            'fee' => 0.00,
            'tax' => 0.00,
            'net_amount' => 120000.00,
            'narration' => 'Software consultancy invoice settlement #892',
            'status' => 'posted',
            'is_aml_flagged' => false,
            'posted_at' => Carbon::now()->subDays(6),
            'authorized_at' => Carbon::now()->subDays(6),
        ]);

        // Sample CTR Transaction (> 2 Million PKR) for Malik Aslam
        Transaction::create([
            'transaction_reference' => 'DEP-20261028-007744',
            'source_account_id' => null,
            'destination_account_id' => $acc3->id,
            'branch_id' => $isbBranch->id,
            'maker_id' => $teller->id,
            'checker_id' => $branchManager->id,
            'transaction_type' => 'cash_deposit',
            'channel' => 'teller',
            'currency' => 'PKR',
            'amount' => 3500000.00,
            'fee' => 0.00,
            'tax' => 0.00,
            'net_amount' => 3500000.00,
            'narration' => 'Commercial cash proceeds deposit - Senator Malik Aslam',
            'status' => 'posted',
            'is_aml_flagged' => true,
            'aml_reason' => 'SBP CTR Rule: Single cash transaction >= PKR 2,000,000 threshold exceeded. | Enhanced Due Diligence triggered for PEP Customer Profile.',
            'ctr_reported' => true,
            'posted_at' => Carbon::now()->subDays(3),
            'authorized_at' => Carbon::now()->subDays(3),
        ]);

        // Sample Utility Bill Payment for Bilal Farooq
        $keBiller = Biller::where('code', 'KELECTRIC')->first();
        BillPayment::create([
            'biller_id' => $keBiller->id,
            'bank_account_id' => $acc4->id,
            'user_id' => $user4->id,
            'consumer_number' => '0400012345678',
            'amount' => 34850.00,
            'late_surcharge' => 0.00,
            'due_date' => Carbon::now()->subDays(10)->toDateString(),
            'billing_month' => 'Oct 2026',
            'transaction_reference' => 'BILL-20261020-008129',
            'status' => 'paid',
            'paid_at' => Carbon::now()->subDays(10),
        ]);

        // Audit Trail Logs
        SecurityAuditLog::create([
            'user_id' => $superAdmin->id,
            'event_type' => 'core_banking_initialized',
            'severity' => 'notice',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Khazana-CBS Bootstrap System Engine',
            'description' => 'Khazana Core Banking Engine (Khazana-CBS) initialized with Chart of Accounts, SBP IBAN Engine, and Maker-Checker Matrix.',
        ]);

        SecurityAuditLog::create([
            'user_id' => $complianceOfficer->id,
            'event_type' => 'aml_ctr_alert',
            'severity' => 'warning',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Khazana-CBS/1.0 Engine',
            'description' => 'Cash Deposit flagged: SBP CTR Rule: Single cash transaction >= PKR 2,000,000 threshold exceeded. | Amount: PKR 3,500,000.00 for A/C ' . $accNo3,
            'payload' => ['account_number' => $accNo3, 'cif' => 'CIF-0030-00103', 'amount' => 3500000.00],
        ]);
    }
}
