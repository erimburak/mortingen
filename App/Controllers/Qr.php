<?php

namespace Controllers;

use Controller;

class Qr extends Controller
{
    public function index()
    {
        $qr = \QRCode\Generator::generateQrCode("data hebele hübele ağağağa");
        $this->response->setContentType(\Response\ContentType::IMAGE_PNG);
        $this->response->setContent($qr);
    }
}