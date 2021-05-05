<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Security;

use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CsrfTokenValidatorTest extends TestCase
{
    private MockObject $csrfTokenManagerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private MockObject $sessionMock;

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

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfToken(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::once())
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->willReturn(true);

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenOnInvalidToken(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::once())
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->willReturn(false);

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenOnMissingTokenHeader(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->setSession($this->sessionMock);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithNotStartedSession(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(false);

        $request = Request::create('/');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithNoCsrfFlag(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set('_nglayouts_no_csrf', true);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        $this->validator->validateCsrfToken($request, 'token_id');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithNoSession(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::never())
            ->method('isStarted');

        $request = Request::create('/');

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithSafeMethod(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }
}
