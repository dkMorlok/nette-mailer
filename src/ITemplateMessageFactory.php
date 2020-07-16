<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

/**
 * @deprecated
 */
interface ITemplateMessageFactory
{
    public function create(string $name, string $lang): TemplateMessage;
}
