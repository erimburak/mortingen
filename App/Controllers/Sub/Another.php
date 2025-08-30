<?php

namespace Controllers\Sub;

class Another extends \Controller
{
    public function index()
    {
        $this->response->addContent("This controller is in subdir.");
    }

    public function SomeAction()
    {
        $this->response->addContent("SomeAction in subdir.");
    }

    public function AnotherAction()
    {
        $this->response->addContent("AnotherAction in subdir.");
    }

    protected function ProtectedAction()
    {
        $this->response->addContent("ProtectedAction in subdir.");
    }

    private function PrivateAction()
    {
        $this->response->addContent("PrivateAction in subdir.");
    }
}