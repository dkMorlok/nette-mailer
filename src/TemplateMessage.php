<?php

namespace Smartsupp\Mailer;

use Nette\Mail;
use Nette\Object;

class TemplateMessage extends Object
{

	/** @var string */
	private $basePath;

	/** @var Template */
	private $template;

	/** @var Mail\Message */
	private $message;


	public function __construct(Template $template, $basePath = null)
	{
		$this->message = new Mail\Message();
		$this->template = $template;
		$this->basePath = $basePath;
	}


	public function getTemplate()
	{
		return $this->template;
	}


	public function getMessage()
	{
		return $this->message;
	}


	public function applyTemplate(array $params)
	{
		$this->template->setParameters($params);
		$this->message->setHtmlBody($this->template, $this->basePath);
	}

}
