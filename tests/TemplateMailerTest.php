<?php

declare(strict_types=1);

namespace Smartsupp\Tests\Mailer;

use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smartsupp\Mailer\IMessageFactory;
use Smartsupp\Mailer\MailerException;
use Smartsupp\Mailer\MessageException;
use Smartsupp\Mailer\TemplateMailer;

class TemplateMailerTest extends TestCase
{
    public function testSendSingle(): void
    {
        /** @var Mailer&MockObject $netteMailer */
        $netteMailer = self::createMock(Mailer::class);
        /** @var IMessageFactory&MockObject $messageFactory */
        $messageFactory = self::createMock(IMessageFactory::class);

        $message = new Message();
        $messageFactory->expects(self::once())
            ->method('createMessage')
            ->with('template', 'sk', ['some' => 'data'], 'from@example.com', [], [])
            ->willReturn($message);

        $netteMailer->expects(self::exactly(2))
            ->method('send')
            ->with($message);

        $mailer = new TemplateMailer($netteMailer, $messageFactory);

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
        /** @var IMessageFactory&MockObject $messageFactory */
        $messageFactory = self::createMock(IMessageFactory::class);

        $message = new Message();
        $messageFactory->expects(self::once())
            ->method('createMessage')
            ->with('template', 'sk', ['some' => 'data'], 'test <from@example.com>', [], [])
            ->willReturn($message);

        $netteMailer->expects(self::once())
            ->method('send')
            ->with($message);

        $mailer = new TemplateMailer($netteMailer, $messageFactory);

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
        /** @var IMessageFactory&MockObject $messageFactory */
        $messageFactory = self::createMock(IMessageFactory::class);

        $messageFactory->expects(self::never())
            ->method('createMessage');

        $netteMailer->expects(self::never())
            ->method('send');

        $mailer = new TemplateMailer($netteMailer, $messageFactory);

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
        /** @var IMessageFactory&MockObject $messageFactory */
        $messageFactory = self::createMock(IMessageFactory::class);

        $messageFactory->expects(self::once())
            ->method('createMessage')
            ->with('template', 'sk', ['some' => 'data'], 'from@example.com', [], [])
            ->willThrowException(new MessageException());

        $netteMailer->expects(self::never())
            ->method('send');

        $mailer = new TemplateMailer($netteMailer, $messageFactory);

        self::expectException(MessageException::class);

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
        /** @var IMessageFactory&MockObject $messageFactory */
        $messageFactory = self::createMock(IMessageFactory::class);

        $message = new Message();
        $messageFactory->expects(self::once())
            ->method('createMessage')
            ->with('template', 'sk', ['some' => 'data'], 'from@example.com', [], [])
            ->willReturn($message);

        $netteMailer->expects(self::once())
            ->method('send')
            ->with($message)
            ->willThrowException(new SendException());

        $mailer = new TemplateMailer($netteMailer, $messageFactory);

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
