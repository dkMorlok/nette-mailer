<?php

namespace Smartsupp\Mailer;

interface ITemplateMessageFactory
{

	/**
	 * Creates template message.
	 * @param string $name
	 * @return TemplateMessage
	 */
	function create($name);

}
