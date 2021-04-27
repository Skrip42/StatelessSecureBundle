<?php
namespace Skrip42\StatelessSecureBundle;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Skrip42\StatelessSecureBundle\Annotation\StatelessSecure;
use ReflectionClass;

class RequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request)
    {
        $routeParams = $request->attributes->get('_route_params');
        if (empty($routeParams['_stateless']) || !$routeParams['_stateless']) {
            return false;
        }
        list($controller, $method) = explode('::', $request->attributes->get('_controller'));
        $reader = new AnnotationReader();

        $reflectionController = new ReflectionClass($controller);
        if ($reader->getClassAnnotation($reflectionController, StatelessSecure::class) !== null
        ) {
            return true;
        };
        if (empty($method) || !$reflectionController->hasMethod($method)) {
            return false;
        }
        $reflectionMethod = $reflectionController->getMethod($method);
        if ($reader->getMethodAnnotation($reflectionMethod, StatelessSecure::class) !== null
        ) {
            return true;
        }
        return false;
    }
}
