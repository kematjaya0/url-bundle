<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Tests\Util;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Translator implements TranslatorInterface 
{
    
    public function trans(string $id, array $parameters = array(), string $domain = null, string $locale = null): string 
    {
        return $id;
    }

}
