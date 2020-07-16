<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

interface ITemplateMailer
{

    /**
     * @param string $template
     * @param string $lang
     * @param mixed[]|array<mixed> $params
     * @param string[]|array<string> $to
     * @param string|null $from
     * @param bool $single True to send one email per recipient, false to send one email to multiple recipients
     * @param string[]|string[][]|array<string, string|array<string, string>> $headers header name => header value
     * @param string[]|array<string, string> $attachments filename => content
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
        array $headers = [],
        array $attachments = []
    ): void;
}
