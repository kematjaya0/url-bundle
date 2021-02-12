<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Tests\Util;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class CsrfTokenManager implements CsrfTokenManagerInterface 
{
    
    public function getToken(string $tokenId): CsrfToken 
    {
        return new CsrfToken($tokenId);
    }

    public function isTokenValid(CsrfToken $token): bool 
    {
        return true;
    }

    public function refreshToken(string $tokenId): CsrfToken 
    {
        return new CsrfToken($tokenId);
    }

    public function removeToken(string $tokenId) 
    {
        
    }

}
