<?php

declare(strict_types=1);

namespace Smartsupp\Tests\Mailer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smartsupp\Mailer\ITemplateFactory;
use Smartsupp\Mailer\LegacyTemplateRenderer;
use Smartsupp\Mailer\Template;
use Smartsupp\Mailer\TemplateRendererException;

class LegacyTemplateRendererTest extends TestCase
{
    public function testRenderBasic(): void
    {
        /** @var Template&MockObject $template */
        $template = self::createMock(Template::class);
        /** @var ITemplateFactory&MockObject $factory */
        $factory = self::createMock(ITemplateFactory::class);
        $renderer = new LegacyTemplateRenderer($factory);

        $template->expects(self::once())
            ->method('setParameters')
            ->with(['data']);

        $template->expects(self::once())
            ->method('renderToString')
            ->willReturn('result');

        $factory->expects(self::once())
            ->method('create')
            ->with('test', 'en')
            ->willReturn($template);

        $result = $renderer->renderTemplate('test', 'en', ['data']);

        self::assertSame('', $result->subject);
        self::assertSame('result', $result->html);
        self::assertSame('', $result->text);
    }

    public function testTemplateRenderToStringThrows(): void
    {
        /** @var Template&MockObject $template */
        $template = self::createMock(Template::class);
        /** @var ITemplateFactory&MockObject $factory */
        $factory = self::createMock(ITemplateFactory::class);
        $renderer = new LegacyTemplateRenderer($factory);

        $template->expects(self::once())
            ->method('setParameters')
            ->with(['data']);

        $template->expects(self::once())
            ->method('renderToString')
            ->willThrowException(new \Exception());

        $factory->expects(self::once())
            ->method('create')
            ->with('test', 'en')
            ->willReturn($template);

        self::expectException(TemplateRendererException::class);

        $renderer->renderTemplate('test', 'en', ['data']);
    }
}
