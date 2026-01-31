<?php

namespace App\Rules;

use App\Models\OtpCode;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOtpCode implements ValidationRule
{
    private string $phoneField;

    private ?string $errorMessage = null;

    /**
     * Create a new rule instance.
     *
     * @param string $phoneField The name of the phone field in the request (e.g., 'phone' or 'phone_from_login')
     */
    public function __construct(string $phoneField = 'phone')
    {
        $this->phoneField = $phoneField;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = request()->all();
        $phone = $data[$this->phoneField] ?? null;

        if (!$phone) {
            $this->errorMessage = 'Telefon belgi tapylmady.';
            $fail($this->errorMessage);

            return;
        }

        // Find LATEST OTP for this phone (only the most recent one)
        $otpCode = OtpCode::where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->first();

        // OTP not found
        if (!$otpCode) {
            $this->errorMessage = 'OTP kod talap edilmedi. Ilki OTP iberiň.';
            $fail($this->errorMessage);

            return;
        }

        // OTP expired - check first before code match
        if (Carbon::now() > $otpCode->valid_until) {
            $this->errorMessage = 'OTP kod wagty tamamlandy. Täze kod iberiň.';
            // Delete expired OTP
            $otpCode->delete();
            $fail($this->errorMessage);

            return;
        }

        // OTP code doesn't match (check against latest OTP only)
        if ($otpCode->code != $value) {
            $this->errorMessage = 'OTP kod nädogry.';
            $fail($this->errorMessage);

            return;
        }

        // All validations passed - delete used OTP
        $otpCode->delete();
    }
}
