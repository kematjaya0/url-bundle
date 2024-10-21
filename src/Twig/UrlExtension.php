<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Twig;

use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Minwork\Helper\Arr;

class UrlExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;
    
    private CredentialStorageInterface $credentialStorage;
    private Security $security;
    
    const KEY_ICON = 'icon';
    const KEY_LABEL= 'label';
    const KEY_ACTION = 'action';
    const KEY_OBJECT = 'object';
    
    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator, CredentialStorageInterface $credentialStorage) 
    {
        $this->urlGenerator = $urlGenerator;
        $this->credentialStorage = $credentialStorage;
        $this->security = $security;
    }
    
    public function getFunctions():array
    {
        return [
            new TwigFunction('link_to', [$this, 'linkTo'], ['is_safe' => ['html']]),
            new TwigFunction('submit_tag', [$this, 'submitTag'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * 
     * @param string $routeName
     * @param array $routeParameters
     * @param array $attributes
     * @param array $granteds
     * @param bool $relative
     */
    public function linkTo(string $routeName, array $routeParameters = [], array $attributes = [], array $granteds = array(), bool $relative = false):?string
    {
        $icon = isset($attributes[self::KEY_ICON]) ? $attributes[self::KEY_ICON] : null;
        $label= isset($attributes[self::KEY_LABEL]) ? $attributes[self::KEY_LABEL] : 'button';
        
        if (!$this->getCredentialStorage()->getAccess($routeName)) {
            
            return null;
        }
        
        if (!$this->isGranted($granteds)) {
            
            return null;
        }
        
        $url = $this->urlGenerator->generate($routeName, $routeParameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        
        return sprintf('<a href="%s" %s>%s %s</a>', $url, $this->generateHTMLAttributes($attributes), $icon, $label);
    }
    
    /**
     * 
     * @param string $routeName
     * @param array $attributes
     * @param array $granteds
     * @return string|null
     */
    public function submitTag(string $routeName, array $attributes = [], array $granteds = array()):?string
    {
        $icon = isset($attributes[self::KEY_ICON]) ? $attributes[self::KEY_ICON] : null;
        $label= isset($attributes[self::KEY_LABEL]) ? $attributes[self::KEY_LABEL] : 'button';
        
        if (!$this->getCredentialStorage()->getAccess($routeName)) {
            
            return null;
        }
        
        if (!$this->isGranted($granteds)) {
            
            return null;
        }
        
        return sprintf('<button type="submit" %s>%s %s</button>', $this->generateHTMLAttributes($attributes), $icon, $label);
    }
    
    /**
     * 
     * @param array $granteds
     * @return bool
     * @throws \Exception
     */
    protected function isGranted(array $granteds = array()):bool
    {
        if (empty($granteds)) {
            
            return true;
        }
        
        if (!isset($granteds[self::KEY_ACTION])) {
            
            throw new \Exception(sprintf("granted key '%s' is required", self::KEY_ACTION));
        }
        
        if (!isset($granteds[self::KEY_OBJECT])) {
            
            throw new \Exception(sprintf("granted key '%s' is required", self::KEY_OBJECT));
        }
        
        return $this->security->isGranted($granteds[self::KEY_ACTION], $granteds[self::KEY_OBJECT]);
    }
    
    /**
     * 
     * @param array $attributes
     * @return string|null
     */
    protected function generateHTMLAttributes(array $attributes = array()):?string
    {
        $htmls= Arr::map($attributes, function ($k, $v) {
            if (self::KEY_ICON === strtolower($k) or self::KEY_LABEL === strtolower($k)) {
                
                return null;
            }
            
            return sprintf('%s="%s"', trim($k), trim($v));
        });
        
        return trim(implode(" ", array_values($htmls)));
    }
    
    
    /**
     * 
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }
    
    /**
     * 
     * @return CredentialStorageInterface
     */
    protected function getCredentialStorage():CredentialStorageInterface
    {
        return $this->credentialStorage;
    }
    
    /**
     * 
     * @return Security
     */
    protected function getSecurity(): Security 
    {
        return $this->security;
    }
}
