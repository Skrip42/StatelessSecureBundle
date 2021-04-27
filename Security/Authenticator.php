<?php
namespace Skrip42\StatelessSecureBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class Authenticator extends AbstractGuardAuthenticator
{
    public function __construct(
        UrlGeneratorInterface $router
    ) {
        $this->router = $router;
    }
    public function supports(Request $request)
    {
        return $request->cookies->get('stateless_token', false);
    }

    public function getCredentials(Request $request)
    {
        return $request->cookies->get('stateless_token');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($credentials === null) {
            throw new CustomUserMessageAuthenticationException(
                'Empty statless token.'
            );
        }
        /** @var StatlessUserProvider $userProvider */
        $user = $userProvider->loadUserByCredentials($credentials);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                'Token not valid.'
            );
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $providerKey
    ) {
        return null;
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ) {
        $data = [
            'success' => false,
            'message' => strtr(
                $exception->getMessageKey(),
                $exception->getMessageData()
            )
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function start(
        Request $request,
        AuthenticationException $authException = null
    ) {
        $response = new RedirectResponse(
            $this->router->generate(
                'stateless_secure_get_token',
                [
                    'target' => $request->getRequestUri()
                ]
            ),
            307
        );
        return $response;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
