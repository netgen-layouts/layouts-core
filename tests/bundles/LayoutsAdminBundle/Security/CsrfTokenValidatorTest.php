<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Security;

use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[CoversClass(CsrfTokenValidator::class)]
final class CsrfTokenValidatorTest extends TestCase
{
    private Stub&CsrfTokenManagerInterface $csrfTokenManagerStub;

    private Stub&SessionInterface $sessionStub;

    private CsrfTokenValidator $validator;

    protected function setUp(): void
    {
        $this->csrfTokenManagerStub = self::createStub(CsrfTokenManagerInterface::class);

        $this->sessionStub = self::createStub(SessionInterface::class);

        $this->validator = new CsrfTokenValidator(
            $this->csrfTokenManagerStub,
        );
    }

    public function testValidateCsrfToken(): void
    {
        $this->csrfTokenManagerStub
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->willReturn(true);

        $this->sessionStub
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionStub);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenOnInvalidToken(): void
    {
        $this->csrfTokenManagerStub
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->willReturn(false);

        $this->sessionStub
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionStub);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenOnMissingTokenHeader(): void
    {
        $this->sessionStub
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->setSession($this->sessionStub);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithNotStartedSession(): void
    {
        $this->sessionStub
            ->method('isStarted')
            ->willReturn(false);

        $request = Request::create('/');
        $request->setSession($this->sessionStub);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithNoCsrfFlag(): void
    {
        $this->sessionStub
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set('_nglayouts_no_csrf', true);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionStub);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithNoSession(): void
    {
        $request = Request::create('/');

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    public function testValidateCsrfTokenWithSafeMethod(): void
    {
        $this->sessionStub
            ->method('isStarted')
            ->willReturn(true);

        $request = Request::create('/');
        $request->setSession($this->sessionStub);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }
}
