<?php
namespace Skrip42\StatelessSecureBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Cache\CacheItemPoolInterface;

class UserProvider implements UserProviderInterface
{
    private $cache;

    public function __construct(
        CacheItemPoolInterface $statelessTokenCache
    ) {
        $this->cache = $statelessTokenCache;
    }

    public function loadUserByUsername($username)
    {
        throw new \Exception('TODO: fill in loadUserByUsername() inside '.__FILE__);
    }

    public function loadUserByCredentials($credentials)
    {
        if (!$this->cache->hasItem($credentials)) {
            return null;
        }
        $user = $this->cache->getItem($credentials)->get();
        if (empty($user)
            || !is_object($user)
            || !in_array(UserInterface::class, class_implements($user))
        ) {
            return null;
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
