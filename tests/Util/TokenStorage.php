<?php

namespace Kematjaya\URLBundle\Tests\Util;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class TokenStorage implements TokenStorageInterface
{
    private $token;
    
    public function getToken() :?TokenInterface
    {
        return $this->token;
    }

    public function setToken(?TokenInterface $token = null) :void
    {
        $this->token = $token;
    }

}
