<?php

namespace Smartsupp\Mailer;

class TemplateMessageFactory implements ITemplateMessageFactory
{

	/** @var ITemplateFactory */
	private $templateFactory;

	/** @var string */
	public $basePath = null;


	public function __construct(ITemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
	}


	/**
	 * Creates template message.
	 * @param string $name
	 * @return TemplateMessage
	 */
	public function create($name)
	{
		$message = new TemplateMessage($this->templateFactory->create($name), $this->basePath);
		return $message;
	}

}
