<?php

namespace App\Service;

use App\Mail\OtpEmail;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;


class OtpService
{
    private SmsServiceSubscription $sendOtpViaSms;

    public function __construct(SmsServiceSubscription $sendOtpViaSms)
    {
        $this->sendOtpViaSms = $sendOtpViaSms;
    }

    // إرسال OTP عبر البريد الإلكتروني
    public function sendOtpPhone($contact, $contact_type): int
    {
        $otp = $this->generateOtp();
        $this->storeOtp($contact, $otp, $contact_type);
        $this->sendOtpViaSms->sendSms([$contact], $otp);
        return $otp;
    }

    private function generateOtp(): int
    {
        return rand(100000, 999999); // توليد رقم عشوائي بين 100000 و 999999
    }

    private function storeOtp($contact, $otp, $contact_type): void
    {
        OtpCode::updateOrCreate(
            ['contact' => $contact, 'type_contact' => $contact_type],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),  // صلاحية 5 دقائق
                'created_at' => Carbon::now(),
            ]
        );
    }


    // تخزين OTP في قاعدة البيانات

    public function sendOtpEmail($contact, $contact_type): int
    {
        $otp = $this->generateOtp();
        $this->storeOtp($contact, $otp, $contact_type);

        Mail::to($contact)->send(new OtpEmail($otp));

        return $otp;
    }


}