<?php

namespace App\Http\Requests\Traits;

trait FormatMoney
{
    private function cleanMoneyValue(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/[^\d]/', '', $value);
        if ($digits === '') {
            return null;
        }

        return (string)($digits / 100);
    }
}
