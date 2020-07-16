<?php

declare(strict_types=1);

namespace Smartsupp\Tests\Mailer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smartsupp\Mailer\ITemplateRenderer;
use Smartsupp\Mailer\TemplateRendererException;
use Smartsupp\Mailer\TemplateRendererSelector;

class TemplateRendererSelectorTest extends TestCase
{
    public function testRenderSuccess(): void
    {
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with('template-name', 'cs', ['some' => 'data'])
            ->willReturn('<html>output</html>');

        $templateRenderers = [
            'template-name' => $renderer,
        ];
        $selector = new TemplateRendererSelector($templateRenderers);

        $result = $selector->renderTemplate('template-name', 'cs', ['some' => 'data']);

        self::assertSame('<html>output</html>', $result);
    }

    public function testRenderSuccessDefault(): void
    {
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::never())
            ->method('renderTemplate');

        /** @var ITemplateRenderer&MockObject $defaultRenderer */
        $defaultRenderer = self::createMock(ITemplateRenderer::class);

        $defaultRenderer->expects(self::once())
            ->method('renderTemplate')
            ->with('bad-template-name', 'cs', ['some' => 'data'])
            ->willReturn('<html>output</html>');

        $templateRenderers = [
            'template-name' => $renderer,
        ];
        $selector = new TemplateRendererSelector($templateRenderers, $defaultRenderer);

        $result = $selector->renderTemplate('bad-template-name', 'cs', ['some' => 'data']);

        self::assertSame('<html>output</html>', $result);
    }

    public function testRenderSuccessNotDefault(): void
    {
        /** @var ITemplateRenderer&MockObject $renderer */
        $renderer = self::createMock(ITemplateRenderer::class);

        $renderer->expects(self::once())
            ->method('renderTemplate')
            ->with('template-name', 'cs', ['some' => 'data'])
            ->willReturn('<html>output</html>');

        /** @var ITemplateRenderer&MockObject $defaultRenderer */
        $defaultRenderer = self::createMock(ITemplateRenderer::class);

        $defaultRenderer->expects(self::never())
            ->method('renderTemplate');

        $templateRenderers = [
            'template-name' => $renderer,
        ];
        $selector = new TemplateRendererSelector($templateRenderers, $defaultRenderer);

        $result = $selector->renderTemplate('template-name', 'cs', ['some' => 'data']);

        self::assertSame('<html>output</html>', $result);
    }

    public function testRenderUnknownTemplateThrows(): void
    {
        $selector = new TemplateRendererSelector([]);

        self::expectException(TemplateRendererException::class);

        $selector->renderTemplate('bad-template-name', 'cs', ['some' => 'data']);
    }

    public function testCreate(): void
    {
        /** @var ITemplateRenderer&MockObject $defaultRenderer */
        $defaultRenderer = self::createMock(ITemplateRenderer::class);

        $renderers = [
            /** @var ITemplateRenderer&MockObject $rendererX */
            'x' => $rendererX = self::createMock(ITemplateRenderer::class),
            /** @var ITemplateRenderer&MockObject $rendererY */
            'y' => $rendererY = self::createMock(ITemplateRenderer::class),
        ];
        $templates = [
            'x' => ['template-x1', 'template-x2'],
            'y' => ['template-y'],
        ];
        $selector = TemplateRendererSelector::create($renderers, $templates, $defaultRenderer);

        $expected = new TemplateRendererSelector([
            'template-x1' => $rendererX,
            'template-x2' => $rendererX,
            'template-y' => $rendererY,
        ], $defaultRenderer);
        self::assertEquals($expected, $selector);
    }
}
