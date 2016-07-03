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


	/**
	 * Is template createable. Returns false if source not exists.
	 * @param $name
	 * @return bool
	 */
	public function isCreateable($name);

}