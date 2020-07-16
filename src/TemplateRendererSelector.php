<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

/**
 * Owns a set of other renderers and proxies rendering to the one defined by "template name to renderer" mapping
 */
class TemplateRendererSelector implements ITemplateRenderer
{

    /** @var ITemplateRenderer[]|array<string, ITemplateRenderer> */
    private array $renderers;


    /**
     * @param ITemplateRenderer[]|array<string, ITemplateRenderer> $renderers Template name => renderer instance
     */
    final public function __construct(array $renderers)
    {
        $this->renderers = $renderers;
    }


    /**
     * @param array<string, ITemplateRenderer>|ITemplateRenderer[] $renderers Renderer name => renderer instance
     * @param array<string, array<string>>|string[][] $templates Renderer name => array of supported template names
     * @return static
     */
    public static function create(array $renderers, array $templates): self
    {
        if (\count($renderers) < 2) {
            throw new \InvalidArgumentException('At least 2 renderers are required.');
        }

        $templateRenderers = [];
        foreach ($templates as $rendererName => $templateNames) {
            if (!isset($renderers[$rendererName])) {
                throw new \InvalidArgumentException(\sprintf('Renderer %s not provided.', $rendererName));
            }

            $renderer = $renderers[$rendererName];

            foreach ($templateNames as $templateName) {
                if (isset($templateRenderers[$templateName])) {
                    throw new \InvalidArgumentException(\sprintf('Template %s already assigned.', $templateName));
                }

                $templateRenderers[$templateName] = $renderer;
            }
        }
        return new static($templateRenderers);
    }


    public function renderTemplate(string $templateName, string $lang, array $params): string
    {
        if (!isset($this->renderers[$templateName])) {
            throw new TemplateRendererException(\sprintf('No renderer for template %s', $templateName));
        }

        $renderer = $this->renderers[$templateName];
        return $renderer->renderTemplate($templateName, $lang, $params);
    }
}
