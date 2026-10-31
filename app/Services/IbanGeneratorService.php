<?php

namespace App\Services;

class IbanGeneratorService
{
    /**
     * SBP Bank Identifier Code (4 characters)
     */
    const BANK_BIC = 'KHZN'; // Khazana Bank

    /**
     * Generate 14-digit local account number.
     * Format: Branch Code (4 digits) + Sequential Number (10 digits)
     */
    public static function generateAccountNumber(string $branchCode, int $sequenceId): string
    {
        $paddedBranch = str_pad(substr(preg_replace('/[^0-9]/', '', $branchCode), 0, 4), 4, '0', STR_PAD_LEFT);
        $paddedSequence = str_pad($sequenceId, 10, '0', STR_PAD_LEFT);
        return $paddedBranch . $paddedSequence;
    }

    /**
     * Generate SBP Compliant 24-Character IBAN.
     * Pakistan IBAN Format: PK + 2 Check Digits + KHZN (4 chars) + 16 numeric digits
     * e.g. PK36KHZN0001000000000001
     */
    public static function generateIban(string $branchCode, string $accountNumber): string
    {
        // 16-digit National Account Number: 4-digit Branch Code + 12-digit Account Part
        $cleanBranch = str_pad(substr(preg_replace('/[^0-9]/', '', $branchCode), 0, 4), 4, '0', STR_PAD_LEFT);
        $cleanAccount = str_pad(substr(preg_replace('/[^0-9]/', '', $accountNumber), -12), 12, '0', STR_PAD_LEFT);
        $bban = self::BANK_BIC . $cleanBranch . $cleanAccount;

        // Modulo 97 Check Digit Calculation (ISO 7064)
        // Move 4 initial chars (PK00) to end: BBAN + P(25) + K(20) + 00
        // K=20, H=17, Z=35, N=23
        $numericString = '20173523' . $cleanBranch . $cleanAccount . '252000';
        
        $remainder = self::bcMod($numericString, 97);
        $checkDigits = str_pad(98 - $remainder, 2, '0', STR_PAD_LEFT);

        return 'PK' . $checkDigits . $bban;
    }

    /**
     * Arbitrary-precision integer modulo implementation for 64-bit PHP.
     */
    private static function bcMod(string $num, int $mod): int
    {
        $rem = 0;
        for ($i = 0; $i < strlen($num); $i++) {
            $rem = ($rem * 10 + (int)$num[$i]) % $mod;
        }
        return $rem;
    }

    /**
     * Validate an IBAN.
     */
    public static function validateIban(string $iban): bool
    {
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $iban));
        if (strlen($clean) !== 24 || substr($clean, 0, 2) !== 'PK') {
            return false;
        }

        $reordered = substr($clean, 4) . substr($clean, 0, 4);
        $numeric = '';
        foreach (str_split($reordered) as $char) {
            if (ctype_alpha($char)) {
                $numeric .= (ord($char) - 55);
            } else {
                $numeric .= $char;
            }
        }

        return self::bcMod($numeric, 97) === 1;
    }
}
