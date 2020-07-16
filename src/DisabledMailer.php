<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

class DisabledMailer implements ITemplateMailer
{
    public function send(
        string $template,
        string $lang,
        array $params,
        array $to,
        ?string $from = null,
        bool $single = true,
        array $headers = [],
        array $attachments = []
    ): void {
    }
}
