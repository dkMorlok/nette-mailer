<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

final class RenderedMessage
{
    public function __construct(
        public readonly string $subject,
        public readonly string $html,
        public readonly string $text,
    ) {
    }
}
