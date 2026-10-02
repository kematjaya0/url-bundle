<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Twig;

use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Minwork\Helper\Arr;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @package Kematjaya\URLBundle\Twig
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class UrlExtension extends AbstractExtension
{
    public const KEY_ICON = 'icon';
    public const KEY_LABEL = 'label';
    public const KEY_ACTION = 'action';
    public const KEY_OBJECT = 'object';

    public function __construct(
        private readonly AuthorizationCheckerInterface $security,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CredentialStorageInterface $credentialStorage
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_to', $this->linkTo(...), ['is_safe' => ['html']]),
            new TwigFunction('submit_tag', $this->submitTag(...), ['is_safe' => ['html']]),
        ];
    }

    public function linkTo(string $routeName, array $routeParameters = [], array $attributes = [], array $granteds = [], bool $relative = false): ?string
    {
        $icon = $attributes[self::KEY_ICON] ?? null;
        $label = $attributes[self::KEY_LABEL] ?? 'button';

        if (!$this->getCredentialStorage()->getAccess($routeName)) {

            return null;
        }

        if (!$this->isGranted($granteds)) {

            return null;
        }

        $url = $this->urlGenerator->generate($routeName, $routeParameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);

        return sprintf('<a href="%s" %s>%s %s</a>', $url, $this->generateHTMLAttributes($attributes), $icon, $label);
    }

    public function submitTag(string $routeName, array $attributes = [], array $granteds = []): ?string
    {
        $icon = $attributes[self::KEY_ICON] ?? null;
        $label = $attributes[self::KEY_LABEL] ?? 'button';

        if (!$this->getCredentialStorage()->getAccess($routeName)) {

            return null;
        }

        if (!$this->isGranted($granteds)) {

            return null;
        }

        return sprintf('<button type="submit" %s>%s %s</button>', $this->generateHTMLAttributes($attributes), $icon, $label);
    }

    /**
     * @throws \Exception
     */
    protected function isGranted(array $granteds = []): bool
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

    protected function generateHTMLAttributes(array $attributes = []): ?string
    {
        $htmls = Arr::map($attributes, function ($k, $v): ?string {
            if (self::KEY_ICON === strtolower($k) or self::KEY_LABEL === strtolower($k)) {

                return null;
            }

            return sprintf('%s="%s"', trim($k), htmlspecialchars(trim((string) $v), ENT_QUOTES, 'UTF-8', false));
        });

        return trim(implode(" ", array_values($htmls)));
    }


    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    protected function getCredentialStorage(): CredentialStorageInterface
    {
        return $this->credentialStorage;
    }

    /**
     * @return Security
     */
    protected function getSecurity(): AuthorizationCheckerInterface
    {
        return $this->security;
    }
}
