<?php

declare(strict_types=1);

namespace Smartsupp\Tests\Mailer;

use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smartsupp\Mailer\ITemplateRenderer;
use Smartsupp\Mailer\MailerException;
use Smartsupp\Mailer\TemplateMailer;
use Smartsupp\Mailer\TemplateRendererException;

class TemplateMailerTest extends TestCase
{
    public function testSendSingle(): void
    {
        /** @var Mailer&MockObject $netteMailer */
        $netteMailer = self::createMock(Mailer::class);
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with('template', 'sk', ['some' => 'data'])
            ->willReturn('<html>body</html>');

        $netteMailer->expects(self::exactly(2))
            ->method('send');

        $mailer = new TemplateMailer($netteMailer, $renderer);

        $mailer->send(
            'template',
            'sk',
            ['some' => 'data'],
            ['receiver1@example.com', 'receiver2@example.com'],
            'from@example.com',
        );
    }

    public function testSendMultiple(): void
    {
        /** @var Mailer&MockObject $netteMailer */
        $netteMailer = self::createMock(Mailer::class);
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with('template', 'sk', ['some' => 'data'])
            ->willReturn('<html>body</html>');

        $netteMailer->expects(self::once())
            ->method('send')
            ->willReturnCallback(function (Message $message) {
                self::assertSame([
                    'receiver1@example.com' => null,
                    'receiver2@example.com' => null
                ], $message->getHeader('To'));
                self::assertSame(['from@example.com' => 'test'], $message->getFrom());
                self::assertSame('<html>body</html>', $message->getHtmlBody());
                return null;
            });

        $mailer = new TemplateMailer($netteMailer, $renderer);

        $mailer->send(
            'template',
            'sk',
            ['some' => 'data'],
            ['receiver1@example.com', 'receiver2@example.com'],
            'test <from@example.com>',
            false
        );
    }

    public function testSendToNoOneDoesNotRender(): void
    {
        /** @var Mailer&MockObject $netteMailer */
        $netteMailer = self::createMock(Mailer::class);
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::never())
            ->method('renderTemplate');

        $netteMailer->expects(self::never())
            ->method('send');

        $mailer = new TemplateMailer($netteMailer, $renderer);

        $mailer->send(
            'template',
            'sk',
            ['some' => 'data'],
            [],
            'test <from@example.com>'
        );
    }

    public function testRenderExceptionIsConverted(): void
    {
        /** @var Mailer&MockObject $netteMailer */
        $netteMailer = self::createMock(Mailer::class);
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with('template', 'sk', ['some' => 'data'])
            ->willThrowException(new TemplateRendererException());

        $netteMailer->expects(self::never())
            ->method('send');

        $mailer = new TemplateMailer($netteMailer, $renderer);

        self::expectException(MailerException::class);

        $mailer->send(
            'template',
            'sk',
            ['some' => 'data'],
            ['receiver1@example.com', 'receiver2@example.com'],
            'from@example.com',
        );
    }

    public function testSendExceptionIsConverted(): void
    {
        /** @var Mailer&MockObject $netteMailer */
        $netteMailer = self::createMock(Mailer::class);
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with('template', 'sk', ['some' => 'data'])
            ->willReturn('<html>body</html>');

        $netteMailer->expects(self::once())
            ->method('send')
            ->willThrowException(new SendException());

        $mailer = new TemplateMailer($netteMailer, $renderer);

        self::expectException(MailerException::class);

        $mailer->send(
            'template',
            'sk',
            ['some' => 'data'],
            ['receiver@example.com'],
            'from@example.com',
        );
    }
}
