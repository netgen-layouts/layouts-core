<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Security;

use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[CoversClass(CsrfTokenValidator::class)]
final class CsrfTokenValidatorTest extends TestCase
{
    private MockObject&CsrfTokenManagerInterface $csrfTokenManagerMock;

    private MockObject&SessionInterface $sessionMock;

    private CsrfTokenValidator $validator;

    protected function setUp(): void
    {
        $this->csrfTokenManagerMock = $this->createMock(
            CsrfTokenManagerInterface::class,
        );

        $this->sessionMock = $this->createMock(SessionInterface::class);

        $this->validator = new CsrfTokenValidator(
            $this->csrfTokenManagerMock,
        );
    }

    public function testValidateCsrfToken(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->once())
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->willReturn(true);

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenOnInvalidToken(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->once())
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->willReturn(false);

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenOnMissingTokenHeader(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->setSession($this->sessionMock);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithNotStartedSession(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(false);

        $request = Request::create('/');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithNoCsrfFlag(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set('_nglayouts_no_csrf', true);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        $this->validator->validateCsrfToken($request, 'token_id');
    }

    public function testValidateCsrfTokenWithNoSession(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->never())
            ->method('isStarted');

        $request = Request::create('/');

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithSafeMethod(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }
}
