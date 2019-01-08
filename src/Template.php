<?php

namespace Smartsupp\Mailer;

use Latte;
use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;

class Template implements ITemplate
{

	/** @var Latte\Engine */
	private $latte;

	/** @var string */
	private $file;

	/** @var array */
	private $params = [];

	/** @var array */
	private $filters = [];


	public function __construct(Latte\Engine $latte)
	{
		$this->latte = $latte;
	}


	/**
	 * Sets the path to the template file.
	 * @param  string
	 * @return self
	 */
	public function setFile(string $file)
	{
		$this->file = $file;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getFile(): string
	{
		return $this->file;
	}


	/**
	 * Registers run-time filter.
	 * @param  string|NULL
	 * @param  callable
	 * @return self
	 */
	public function addFilter($name, $callback)
	{
		return $this->latte->addFilter($name, $callback);
	}


	/**
	 * Registers after render filter.
	 * @param  callable
	 * @return self
	 */
	public function addAfterFilter($callback)
	{
		$this->filters[] = $callback;
		return $this;
	}


	/**
	 * Renders template to output.
	 * @return string
	 */
	public function render()
	{
		$string = $this->latte->renderToString($this->file, $this->params);
		foreach ($this->filters as $filter) {
			$string = $filter($string);
		}
		return $string;
	}


	/**
	 * Sets all parameters.
	 * @param  array
	 * @return self
	 */
	public function setParameters(array $params)
	{
		$this->params = $params + $this->params;
		return $this;
	}


	/**
	 * Sets translate adapter.
	 * @param ITranslator $translator
	 * @return self
	 */
	public function setTranslator(ITranslator $translator = null)
	{
		$this->latte->addFilter('translate', $translator === null ? null : array($translator, 'translate'));
		return $this;
	}


	/**
	 * Returns array of all parameters.
	 * @return array
	 */
	public function getParameters()
	{
		return $this->params;
	}


	/**
	 * Sets a template parameter. Do not call directly.
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value)
	{
		$this->params[$name] = $value;
	}

}
