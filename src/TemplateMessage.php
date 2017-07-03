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


	/**
	 * @return Template
	 */
	public function getTemplate()
	{
		return $this->template;
	}


	/**
	 * @return Mail\Message
	 */
	public function getMessage()
	{
		return $this->message;
	}


	/**
	 * @param array $params
	 * @return string
	 */
	public function applyTemplate(array $params)
	{
		$this->template->setParameters($params);
		$string = $this->template->render();
		$this->message->setHtmlBody($string, $this->basePath);
		return $string;
	}

}
