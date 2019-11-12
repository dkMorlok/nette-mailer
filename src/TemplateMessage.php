<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

use Nette\Mail;

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
	public function getMail()
	{
		return $this->mail;
	}


	/**
	 * @param array $params
	 */
	public function setParameters(array $params)
	{
		$this->template->setParameters($params);
	}


	/**
	 * Render template and set html body
	 * @return string
	 */
	public function render()
	{
		$string = $this->template->render();
		$this->mail->setHtmlBody($string, $this->basePath);
		return $string;
	}

}
