<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

use Nette\Mail\Message;

class MessageFactory implements IMessageFactory
{
    private ITemplateRenderer $renderer;

    /** @var string[]  */
    private array $emails;

    private ?string $basePath;

    /**
     * @param ITemplateRenderer $renderer
     * @param string[] $emails
     * @param string|null $basePath
     */
    public function __construct(
        ITemplateRenderer $renderer,
        array $emails = [],
        ?string $basePath = null
    ) {
        $this->renderer = $renderer;
        $this->emails = $emails;
        $this->basePath = $basePath;
    }

    public function createMessage(
        string $template,
        string $lang,
        array $params,
        ?string $from = null,
        array $headers = [],
        array $attachments = []
    ): Message {

        $message = new Message();

        foreach ($headers as $name => $value) {
            $message->setHeader($name, $value);
        }

        if ($from === null) {
            $message->setFrom($this->emails['default']);
        } elseif (isset($this->emails[$from])) {
            $message->setFrom($this->emails[$from]);
        } else {
            $message->setFrom($from);
        }

        foreach ($attachments as $name => $content) {
            $message->addAttachment($name, $content);
        }

        try {
            $body = $this->renderer->renderTemplate($template, $lang, $params);
        } catch (TemplateRendererException $e) {
            throw new MessageException('Template rendering failed: ' . $e->getMessage(), $e->getCode(), $e);
        }

        $message->setHtmlBody($body, $this->basePath);
        return $message;
    }
}
