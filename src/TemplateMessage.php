<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

use Nette\Mail;

/**
 * @deprecated
 */
class TemplateMessage
{

    /** @var string */
    private $basePath;

    /** @var Template */
    private $template;

    /** @var Mail\Message */
    private $mail;


    public function __construct(Template $template, $basePath = null)
    {
        $this->mail = new Mail\Message();
        $this->template = $template;
        $this->basePath = $basePath;
    }


    public function getTemplate(): Template
    {
        return $this->template;
    }


    public function getMail(): Mail\Message
    {
        return $this->mail;
    }


    public function setParameters(array $params): void
    {
        $this->template->setParameters($params);
    }


    public function render(): string
    {
        $string = $this->template->renderToString();
        $this->mail->setHtmlBody($string, $this->basePath);
        return $string;
    }
}
