<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

interface ITemplateMailer
{

    /**
     * @param string $template
     * @param string $lang
     * @param array $params
     * @param array $to
     * @param string|null $from
     * @param bool $single
     * @param array $headers
     *
     * @throws MailerException
     */
    public function send(
        string $template,
        string $lang,
        array $params,
        array $to,
        ?string $from = null,
        bool $single = true,
        array $headers = []
    ): void;
}
