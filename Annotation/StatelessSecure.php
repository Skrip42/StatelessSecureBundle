<?php
namespace Skrip42\StatelessSecureBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 *
 * @Target({"CLASS", "METHOD"})
 */
class StatelessSecure
{
    public $value = true;
}
