# Khazana-CBS (خزانہ کور بینکنگ سسٹم)
### Enterprise Core Banking System & Digital Financial Services Engine

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![SBP](https://img.shields.io/badge/State%20Bank%20of%20Pakistan-Compliant-006600?style=for-the-badge)](https://www.sbp.org.pk)

**Khazana-CBS** is an enterprise-grade Core Banking System (CBS) and Digital Internet Banking platform developed for Pakistani commercial banking and microfinance institutions. It complies with State Bank of Pakistan (SBP) regulatory directives, including BPRD regulations, SBP PRISM/RTGS interbank settlement, 1LINK switch interoperability, Raast instant P2P payments, AML/CFT surveillance, and mandatory Currency Transaction Reporting (CTR).

---

## 🏛️ Architectural Overview & Core Capabilities

```
+-----------------------------------------------------------------------------------+
|                        Khazana-CBS Unified Platform                               |
+-----------------------------------------------------------------------------------+
            |                                                   |
            v                                                   v
+-------------------------------+               +-----------------------------------+
|   CBS Head Office & Branch   |               |   Customer Digital Banking        |
|   Operations Cockpit          |               |   Internet & Mobile Portal        |
+-------------------------------+               +-----------------------------------+
| • Customer Master File (CIF)  |               | • Biometric Login & Security PIN  |
| • SBP Mod-97 IBAN Generator   |               | • Real-Time Available Balance     |
| • Four-Eyes Maker-Checker     |               | • Certified PDF/Print Statements  |
| • Teller Cash Desk (Till)     |               | • Instant Transfers (Internal)    |
| • SBP AML / CFT & CTR Engine  |               | • 1LINK Interbank IBFT            |
| • Real-Time Trial Balance     |               | • SBP Raast Instant P2P Payment   |
| • Storno Reversal Bookkeeping |               | • 1BILL Utility & FBR Tax Payment |
| • End-of-Day (EOD) Batch      |               | • Virtual Debit Card Controls     |
+-------------------------------+               +-----------------------------------+
            |                                                   |
            +-------------------------+-------------------------+
                                      |
                                      v
            +---------------------------------------------------+
            |          Double-Entry General Ledger (GL)         |
            |   Assets (1000s) = Liabilities + Equity (2000s)   |
            +---------------------------------------------------+
```

---

## 💎 Key System Modules

### 1. General Ledger & Chart of Accounts (COA)
- **Strict Double-Entry Bookkeeping:** Every transaction records matching debits and credits ($\sum\text{Debits} = \sum\text{Credits}$).
- **Preconfigured Pakistani Core GL Classes:**
  - `1010`: Cash in Branch Vaults & Tills (Asset)
  - `1020`: Statutory Reserves with State Bank of Pakistan (Asset)
  - `1030`: 1LINK & Raast Interbank Clearing Account (Asset)
  - `1040`: Consumer & Auto Financing Portfolio (Asset)
  - `2010`: Customer Current Demand Deposits (Liability)
  - `2020`: Customer PLS Savings Deposits (Liability)
  - `2030`: Term Deposit Receipts - TDR (Liability)
  - `2040`: 1BILL Utility Collections Clearing (Liability)
  - `3010`: SBP MCR Paid-Up Capital (Equity)
  - `4010`: Commission, IBFT & Transaction Fee Income (Revenue)
  - `5010`: PLS Savings Account Profit Paid (Expense)
- **Real-Time Trial Balance:** Instant live verification indicator confirming total GL debits equal total GL credits.
- **Storno Accounting Principles:** SBP prohibits deleting financial transactions. Any erroneous posting is corrected via equal and opposite reversing entries referencing the original batch.

### 2. SBP Compliant 24-Character IBAN Engine
- Implements **ISO 7064 Modulo 97-10** check-digit computation for Pakistan:
  $$\text{IBAN} = \text{PK} + \text{CheckDigits} + \text{KHZN} + \text{BranchCode(4)} + \text{AccountSeq(12)}$$
- Automatically validates structure, national bank identifier (`KHZN`), and account integrity on funds transfers.

### 3. Four-Eyes Maker-Checker Governance Matrix
- Mandatory dual-authorization principle for high-risk operations:
  - Transactions $\ge$ PKR 100,000.00
  - Accounts freeze or unfreeze mandates
  - Manual General Ledger adjustments
- Enforces strict segregation of duties: Maker cannot act as Checker on their own submission.

### 4. Anti-Money Laundering (AML/CFT) & SBP CTR Surveillance
- **SBP Mandatory CTR Rule:** Any cash deposit or cash withdrawal $\ge$ PKR 2,000,000 automatically triggers Currency Transaction Reporting (CTR) for disclosure to the Financial Monitoring Unit (FMU).
- **24-Hour Velocity Tracking:** Triggers surveillance alerts if cumulative volume over 24 hours exceeds PKR 5,000,000.
- **Politically Exposed Persons (PEP):** Enhanced Due Diligence (EDD) registry for high-profile government, parliamentary, and judiciary account holders.
- **Forensic Audit Trail:** Tracks IP address, user-agent, severity, and forensic payload for every critical action.

### 5. Teller Cash Desk
- Branch vault limit tracking and current physical till balance display.
- One-click Cash Deposit and Cash Withdrawal with instant balance updates and receipt print preview.

### 6. End-of-Day (EOD) Batch Engine
- Automatic calculation of daily accrued profit on PLS Savings accounts:
  $$\text{Daily Profit} = \frac{\text{Available Balance} \times \text{Profit Rate}}{365}$$
- Term Deposit Receipts (TDR) maturity processing and automatic tenor roll-overs.
- Branch vault reconciliation and General Ledger balance certification.

### 7. Customer Digital Internet Banking Portal
- **Dashboard:** Interactive ATM Debit Card visual, spendable balance, recent transactions mini-statement.
- **Certified Statement:** Official bank statement with date filters, debit/credit columns, and printer/PDF layout.
- **Instant Transfers:**
  - Internal Khazana account-to-account (0 fee, real-time)
  - 1LINK IBFT to commercial banks (HBL, Meezan, UBL, MCB, Alfalah)
  - SBP Raast Instant P2P Payment via registered mobile numbers
- **1BILL Utility Payments:** K-Electric, LESCO, SSGC, SNGPL, PTCL, and FBR PSID tax challans.
- **Virtual Debit Card Management:** Self-service instant card freeze, eCommerce toggle, international payments toggle, and daily POS/ATM limit adjustments.
- **Cheque Book Services:** Requisition for 25, 50, or 100 leaves with individual leaf clearing status.
- **Consumer Loan Simulator:** Real-time EMI calculation with dynamic amortization breakdown for Auto, Home Murabaha, and Solar financing.

---

## 🚀 Quick Start Guide

### 1. Requirements
- PHP 8.2 or higher
- SQLite 3 (or MySQL 8.0+)
- Composer

### 2. Setup
```bash
# Clone the repository
git clone https://github.com/syedmujtabatech/khazana-cbs.git
cd khazana-cbs

# Install PHP dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed real Pakistani banking master data
php artisan migrate:fresh --seed

# Start development server
php artisan serve --host=0.0.0.0 --port=8090
```

Access the platform at: **[http://localhost:8090](http://localhost:8090)**

---

## 🔑 Demo Access & Persona Credentials

All test accounts share the default password: `Bank@1234`
Transaction Security PIN for customer transfers: `1122` or `1234`

| Role | Name | Email | Password | Access Portal |
|---|---|---|---|---|
| **Super Admin / CTO** | Syed Mujtaba Arshad | `admin@khazanabank.pk` | `Bank@1234` | Head Office CBS |
| **Branch Manager (Checker)** | Kamran Hashmi | `manager.khi@khazanabank.pk` | `Bank@1234` | Head Office CBS |
| **Operations Officer (Maker)** | Zubair Qureshi | `maker.khi@khazanabank.pk` | `Bank@1234` | Head Office CBS |
| **Cashier / Teller** | Ayesha Siddiqui | `teller.khi@khazanabank.pk` | `Bank@1234` | Teller Cash Desk |
| **AML Compliance Officer** | Waqar Ahmed Mir | `compliance@khazanabank.pk` | `Bank@1234` | Compliance Cockpit |
| **Customer (Tech Architect)** | Bilal Farooq | `bilal.farooq@example.com` | `Bank@1234` | Internet Banking |
| **Customer (Business Current)** | Tariq Mehmood | `tariq.mehmood@example.com` | `Bank@1234` | Internet Banking |
| **Customer (Senator - PEP)** | Malik Aslam | `malik.aslam@example.com` | `Bank@1234` | Internet Banking |

> **Pro Tip:** The login screen and top navigation bar include a **1-Click "Switch Role"** dropdown that instantly switches context between any of these roles without re-typing credentials!

---

## 🛡️ License & Regulatory Notice

Developed for educational, research, and financial software evaluation purposes under Pakistani banking regulations (State Bank of Pakistan BPRD guidelines).

Designed and Engineered by **Syed Mujtaba Arshad**.
