<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

use Nette\Bridges\ApplicationLatte\ILatteFactory;

class TemplateFactory implements ITemplateFactory
{

	/** @var string */
	public $templatesDir;

	/** @var ILatteFactory */
	private $latteFactory;

	/** @var array */
	private $defaultParameters = [];


	public function __construct(ILatteFactory $latteFactory)
	{
		$this->latteFactory = $latteFactory;
	}


	public function setDefaultParameters(array $defaultParameters)
	{
		$this->defaultParameters = $defaultParameters;
	}


	public function create(string $name): Template
	{
		$template = new Template($this->createTemplateEngine());
		$template->setFile($this->formatTemplateName($name));
		$template->setParameters($this->defaultParameters);
		return $template;
	}


	protected function createTemplateEngine()
	{
		return $this->latteFactory->create();
	}


	protected function formatTemplateName($name)
	{
		return $this->templatesDir . '/' . $name . '.latte';
	}

}
