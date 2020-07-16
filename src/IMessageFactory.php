<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

use Nette\Mail\Message;

interface IMessageFactory
{
    /**
     * @param string $template
     * @param string $lang
     * @param mixed[]|array<mixed> $params
     * @param string|null $from
     * @param string[]|string[][]|array<string, string|array<string, string>> $headers header name => header value
     * @param string[]|array<string, string> $attachments filename => content
     * @return Message
     *
     * @throws MessageException
     */
    public function createMessage(
        string $template,
        string $lang,
        array $params,
        ?string $from = null,
        array $headers = [],
        array $attachments = []
    ): Message;
}
