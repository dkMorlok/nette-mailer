<?php

namespace Smartsupp\Mailer;

interface ITemplateFactory
{

	/**
	 * Create template by name
	 * @param string $name
	 * @return Template
	 */
	function create($name);


	/**
	 * Returns file exists
	 * @param string $name
	 * @return boolean
	 */
	function exists($name);

}