<?php

namespace Kematjaya\URLBundle\Tests\Unit\Twig;

use Kematjaya\URLBundle\Storage\CollectionCredentialStorage;
use Kematjaya\URLBundle\Twig\DeleteExtension;
use Kematjaya\URLBundle\Twig\UrlExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UrlExtensionTest extends TestCase
{
    private CollectionCredentialStorage $storage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private MockObject $authorizationChecker;

    protected function setUp(): void
    {
        $this->storage = new CollectionCredentialStorage();
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
    }

    private function createUrlGenerator(): UrlGeneratorInterface
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(fn(string $name, array $params = []): string => '/' . $name . ($params ? '?' . http_build_query($params) : ''));

        return $urlGenerator;
    }

    private function createExtension(): UrlExtension
    {
        return new UrlExtension($this->authorizationChecker, $this->createUrlGenerator(), $this->storage);
    }

    public function testLinkToRendersAnchorWithoutIconAndLabelAttributes(): void
    {
        $html = $this->createExtension()->linkTo('item_edit', ['id' => 1], [
            'class' => 'btn', 'icon' => '<i class="fa"></i>', 'label' => 'Edit',
        ]);

        $this->assertSame('<a href="/item_edit?id=1" class="btn"><i class="fa"></i> Edit</a>', $html);
    }

    public function testAttributeValuesAreEscaped(): void
    {
        $html = $this->createExtension()->linkTo('item_edit', [], ['title' => '"><script>x</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('title="&quot;&gt;&lt;script&gt;x&lt;/script&gt;"', $html);
    }

    public function testLinkToReturnsNullWhenRouteIsDenied(): void
    {
        $this->storage->setAccess('item_edit', false);

        $this->assertNull($this->createExtension()->linkTo('item_edit'));
    }

    public function testGrantedsAreCheckedWithAuthorizationChecker(): void
    {
        $object = new \stdClass();
        $this->authorizationChecker->expects($this->exactly(2))->method('isGranted')
            ->with('update', $object)
            ->willReturnOnConsecutiveCalls(true, false);
        $extension = $this->createExtension();

        $this->assertNotNull($extension->linkTo('item_edit', [], [], ['action' => 'update', 'object' => $object]));
        $this->assertNull($extension->linkTo('item_edit', [], [], ['action' => 'update', 'object' => $object]));
    }

    public function testGrantedsRequireActionKey(): void
    {
        $this->expectExceptionMessage("granted key 'action' is required");

        $this->createExtension()->linkTo('item_edit', [], [], ['object' => new \stdClass()]);
    }

    public function testSubmitTag(): void
    {
        $html = $this->createExtension()->submitTag('item_save', ['class' => 'btn', 'label' => 'Save']);

        $this->assertSame('<button type="submit" class="btn"> Save</button>', $html);
    }

    public function testDeleteTagContainsMethodAndToken(): void
    {
        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $tokenManager->method('getToken')->with('delete1')->willReturn(new CsrfToken('delete1', 'abc'));
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $extension = new DeleteExtension($translator, $tokenManager, $this->authorizationChecker, $this->createUrlGenerator(), $this->storage);

        $html = $extension->deleteTag('delete1', 'item_delete', ['id' => 1]);

        $this->assertStringContainsString('action="/item_delete?id=1"', $html);
        $this->assertStringContainsString('name="_method" value="DELETE"', $html);
        $this->assertStringContainsString('name="_token" value="abc"', $html);
        $this->assertStringContainsString("confirm('delete_confirm_?')", $html);
    }
}
