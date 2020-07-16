<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

interface ITemplateRenderer
{

    /**
     * @param string $templateName
     * @param string $lang
     * @param array $params
     * @return string
     *
     * @throws TemplateRendererException if unknown template, lang or inappropriate params are passed
     */
    public function renderTemplate(string $templateName, string $lang, array $params): string;
}
