<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    #[\Override] public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && str_starts_with(
                $request->headers->get('Authorization'),
                'Bearer '
            );
    }

    #[\Override] public function authenticate(Request $request): Passport
    {
        $identifier = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        return new SelfValidatingPassport(new UserBadge($identifier));
    }

    #[\Override] public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        return null;
    }

    #[\Override] public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?JsonResponse {
        return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}
