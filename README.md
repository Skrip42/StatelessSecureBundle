# StatelessSecureBundle

add security token to you stateless route

## install

```bash
composer require skrip42/stateless-secure-bundle
```

then add stateless token gate to you routes.yaml

```yaml
#routes.yaml
stateless_secure:
  resource: '@StatelessSecureBundle/Resources/config/routes.yaml'
```

make sure the route 'stateless_secure_get_token' is not accessible as public

configure you security.yaml

```yaml
#security.yaml
security:
  providers:
    stateless_user_provider:
      id: Skrip42\StatelessSecureBundle\Security\UserProvider
  firewalls:
    stateless:
      anonymous: true
      stateless: true
      request_matcher: Skrip42\StatelessSecureBundle\RequestMatcher
      provider: stateless_user_provider
      guard:
        authenticators:
          - Skrip42\StatelessSecureBundle\Security\Authenticator
```

optional redeclare you own cache pool:

```yaml
#cache.yaml

framework:
  cache:
    default_redis_provider: 'redis://redis:6379'
    pools:
      stateless_token.cache:
        adapter: cache.adapter.redis
```

## usage

just add annotation @StatelessSecure to you target action:

```php
    /**
     * @Route(
     *   "/some_path",
     *   name="some_name",
     *   stateless=true
     * )
     * @StatelessSecure
     */
    public function sameAction(Request $request) : Response
    {
        ...
```
