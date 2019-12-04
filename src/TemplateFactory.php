<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

use Latte\Engine;
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


	public function setDefaultParameters(array $defaultParameters): void
	{
		$this->defaultParameters = $defaultParameters;
	}


	public function create(string $name, string $lang): Template
	{
		$template = new Template($this->createTemplateEngine());
		$template->setFile($this->formatTemplateName($name, $lang));
		$template->setParameters($this->defaultParameters);
		return $template;
	}


	protected function createTemplateEngine(): Engine
	{
		return $this->latteFactory->create();
	}


	protected function formatTemplateName(string $name, string $lang): string
	{
		return "{$this->templatesDir}/{$name}.latte";
	}

}
