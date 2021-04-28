<?php
namespace Skrip42\StatelessSecureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use DateInterval;
use Symfony\Component\HttpFoundation\Cookie;
use DateTime;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @Route("/stateless_secure", name="stateless_secure")
 */
class StatelessSecureController extends AbstractController
{
    const TOKEN_LIFTIME = 15 * 60;

    /**
     * @Route("/get_token", name="_get_token")
     */
    public function getToken(
        Request $request,
        CacheItemPoolInterface $statelessTokenCache
    ) {
        /** @var App\Security\User $user */
        $user = $this->getUser();
        $token = uniqid("", true);
        $item = $statelessTokenCache->getItem($token);
        $item->set($user);
        $item->expiresAfter(self::TOKEN_LIFTIME * 2);
        $statelessTokenCache->save($item);
        $targetRoute = $request->query->get('target');
        $response = new RedirectResponse($targetRoute, 307);
        $expired = new DateTime('now');
        $expired->add(new DateInterval('PT' . self::TOKEN_LIFTIME . 'S'));
        $response->headers->setCookie(new Cookie('stateless_token', $token, $expired));
        return $response;
    }
}
