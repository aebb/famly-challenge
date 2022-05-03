<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;

abstract class RequestModel
{
    protected Request $request;

    protected string $apiToken;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->apiToken = $request->headers->get('X-AUTH-TOKEN') ?? '';
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getToken(): ?string
    {
        return $this->apiToken;
    }
}
