<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwilioVerifyService
{

    protected $url = "https://verify.twilio.com/v2/Services/";
    protected $sid = "";
    protected $service = "";
    protected $token = "";

    public function generate() {
        $response = Http::asForm()
            ->withBasicAuth($this->sid, $this->token)
            ->post("{$this->url}{$this->service}/Verifications", [
                'To' => '',
                'Channel' => 'sms',
            ]);
    }

    public function verify($code) {
        $response = Http::asForm()
            ->withBasicAuth($this->sid, $this->token)
            ->post("{$this->url}{$this->service}/VerificationCheck", [
                'To' => '',
                'Code' => $code,
            ]);

        return $response->object()->valid ?? false;
    }
}