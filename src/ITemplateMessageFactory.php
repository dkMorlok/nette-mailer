<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

interface ITemplateMessageFactory
{

	function create(string $name): TemplateMessage;

}
