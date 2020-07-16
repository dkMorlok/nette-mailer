<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

class LegacyTemplateRenderer implements ITemplateRenderer
{

    private ITemplateFactory $templateFactory;


    public function __construct(ITemplateFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }


    public function renderTemplate(string $templateName, string $lang, array $params): string
    {
        $template = $this->templateFactory->create($templateName, $lang);

        if (isset($params['filters'])) {
            foreach ($params['filters'] as $name => $callback) {
                $template->addFilter($name, $callback);
            }
            unset($params['filters']);
        }
        if (isset($params['attachments'])) {
            unset($params['attachments']);
        }

        $template->setParameters($params);

        try {
            return $template->renderToString();
        } catch (\Throwable $e) {
            throw new TemplateRendererException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
