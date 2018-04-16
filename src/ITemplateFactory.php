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

}
