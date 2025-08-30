<?php

namespace Controllers;

use Controller;
use TwoFactorAuth\TwoFactorAuth;

class Tfa extends Controller
{
    public function index()
    {
        TwoFactorAuth::generateSession();
        // $this->response->setContent(TwoFactorAuth::getSecret());
        $data = TwoFactorAuth::qrData("username", "domain", TwoFactorAuth::getSecret(), "issuer");

        $qr = \QRCode\Generator::generateQrCode($data);
        $this->response->setContentType(\Response\ContentType::IMAGE_PNG);
        $this->response->setContent($qr);
    }
}
