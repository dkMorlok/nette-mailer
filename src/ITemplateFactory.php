<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

interface ITemplateFactory
{

	function create(string $name): Template;

}
