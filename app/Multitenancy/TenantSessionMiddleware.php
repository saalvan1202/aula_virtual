<?php

namespace App\Multitenancy;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TenantSessionMiddleware extends StartSession
{

    protected $schemaNameSession;

    public function setSchemaNameSession(Session $session)
    {
        $this->schemaNameSession=$session->getName();
        if(is_multitenancy()){
            $this->schemaNameSession=$this->schemaNameSession.'_'.schema_name();
        }

    }
    public function getSession(Request $request)
    {
        return tap($this->manager->driver(), function ($session) use ($request) {
            $this->setSchemaNameSession($session);
            $session->setId($request->cookies->get($this->schemaNameSession));
        });
    }
    protected function addCookieToResponse(Response $response, Session $session)
    {

        if ($this->sessionIsPersistent($config = $this->manager->getSessionConfig())) {
            $response->headers->setCookie(new Cookie(
                $this->schemaNameSession, $session->getId(), $this->getCookieExpirationDate(),
                $config['path'], $config['domain'], $config['secure'] ?? false,
                $config['http_only'] ?? true, false, $config['same_site'] ?? null
            ));
        }
    }
}
