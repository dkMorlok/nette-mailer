<?php declare(strict_types = 1);

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


	public function create(string $name, string $lang): TemplateMessage
	{
		return new TemplateMessage($this->templateFactory->create($name, $lang), $this->basePath);
	}

}
