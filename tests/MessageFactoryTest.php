<?php

declare(strict_types=1);

namespace Smartsupp\Tests\Mailer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smartsupp\Mailer\ITemplateRenderer;
use Smartsupp\Mailer\MessageException;
use Smartsupp\Mailer\MessageFactory;
use Smartsupp\Mailer\TemplateRendererException;

final class MessageFactoryTest extends TestCase
{
    public function testCreateMessage(): void
    {
        $emails = ['default' => 'default@email.test'];
        $basePath = '/base/path';
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $template = 'template-name';
        $lang = 'cs';
        $params = ['some' => 'data'];
        $renderedTemplate = '<html>out</html>';
        $from = 'from-name <from@email.test>';
        $headers['Cc'] = 'cc@email.test';
        $attachments = [
            'test.txt' => 'hello',
        ];

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with($template, $lang, $params)
            ->willReturn($renderedTemplate);

        $messageFactory = new MessageFactory($renderer, $emails, $basePath);

        $message = $messageFactory->createMessage(
            $template,
            $lang,
            $params,
            $from,
            $headers,
            $attachments
        );

        self::assertSame($renderedTemplate, $message->getHtmlBody());
        self::assertSame(['from@email.test' => 'from-name'], $message->getFrom());
        self::assertArrayHasKey('Cc', $message->getHeaders());
        self::assertSame($headers['Cc'], $message->getHeaders()['Cc']);
        self::assertCount(\count($attachments), $message->getAttachments());
    }

    public function testThrowsIfRendererThrows(): void
    {
        $emails = ['default' => 'default@email.test'];
        $basePath = '/base/path';
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $template = 'template-name';
        $lang = 'cs';
        $params = ['some' => 'data'];
        $from = 'from-name <from@email.test>';
        $headers['Cc'] = 'cc@email.test';
        $attachments = [
            'test.txt' => 'hello',
        ];

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with($template, $lang, $params)
            ->willThrowException(new TemplateRendererException());

        $messageFactory = new MessageFactory($renderer, $emails, $basePath);

        self::expectException(MessageException::class);

        $messageFactory->createMessage(
            $template,
            $lang,
            $params,
            $from,
            $headers,
            $attachments
        );
    }
}
