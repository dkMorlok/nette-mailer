<?php

namespace Smartsupp\Mailer;

use Nette\Mail;

class TemplateMessage extends Mail\Message
{

	/** @var string */
	private $basePath;

	/** @var Template */
	private $template;


	public function __construct(Template $template, $basePath = null)
	{
		parent::__construct();
		$this->template = $template;
		$this->basePath = $basePath;
	}


	public function getTemplate()
	{
		return $this->template;
	}


	public function applyTemplate()
	{
		$this->setHtmlBody($this->template, $this->basePath);
	}

}
