<?php

/**
 * This file is part of the helpdesk.
 */

namespace Kematjaya\URLBundle\Twig;

use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Twig\TwigFunction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @package App\URLBundle\Twig
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DeleteExtension extends UrlExtension
{
    
    /**
     * 
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;
    
    /**
     * 
     * @var TranslatorInterface
     */
    private $translator;
    
    public function __construct(TranslatorInterface $translator, CsrfTokenManagerInterface $tokenGenerator, Security $security, UrlGeneratorInterface $urlGenerator, CredentialStorageInterface $credentialStorage) 
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->translator = $translator;
        parent::__construct($security, $urlGenerator, $credentialStorage);
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('delete_tag', [$this, 'deleteTag'], ['is_safe' => ['html']])
        ];
    }
    
    public function deleteTag(string $tokenId, string $routeName, array $routeParameters = [], array $attributes = [], array $granteds = array(), array $extraContent = array(), bool $relative = false):?string
    {
        $attributes[self::KEY_LABEL] = isset($attributes[self::KEY_LABEL]) ? $attributes[self::KEY_LABEL] : 'delete';
        
        if (!$this->getCredentialStorage()->getAccess($routeName)) {
            
            return null;
        }
        
        if (!$this->isGranted($granteds)) {
            
            return null;
        }
        
        $url = $this->getUrlGenerator()->generate($routeName, $routeParameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        return sprintf('<form method="post" action="%s" onsubmit="return confirm(\'%s\');">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="%s">
                    %s 
                    %s
                </form>', $url, $this->translator->trans('delete_confirm_?'), $this->tokenGenerator->getToken($tokenId)->getValue(), implode(" ", $extraContent), $this->submitTag($routeName, $attributes, $granteds));
    }
}
