<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Templating\EngineInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    protected $templating;

    /**
     * AccessDeniedHandler constructor.
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return new Response($this->templating->render('errors/access_denied.html.twig'), 403);
    }
}