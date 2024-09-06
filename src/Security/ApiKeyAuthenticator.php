<?php

namespace SyncShopifyBundle\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        #[Autowire('%env(SYNC_SHOPIFY_BUNDLE_API_KEY)%')]
        private readonly string $apiKey)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('x-api-key');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('x-api-key');
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        if ($apiKey !== $this->apiKey) {
            throw new CustomUserMessageAuthenticationException('Invalid API KEY');
        }

        return new SelfValidatingPassport(new UserBadge("admin"));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
