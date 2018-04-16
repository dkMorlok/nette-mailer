<?php

namespace Smartsupp\Mailer;

use Nette\DI\CompilerExtension;

class MailerExtension extends CompilerExtension
{

	public $defaults = [
		'enabled' => true,
		'mailer' => '@mail.mailer',
		'emails' => [],
		'basePath' => null,
		'templatesDir' => null,
		'params' => [],
	];


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$container->addDefinition($this->prefix('templateFactory'))
			->setClass('Smartsupp\Mailer\TemplateFactory')
			->addSetup('setDefaultParameters', [$config['params']])
			->addSetup('$templatesDir', [$config['templatesDir']]);

		$messageFactory = $container->addDefinition($this->prefix('messageFactory'))
			->setClass('Smartsupp\Mailer\TemplateMessageFactory')
			->addSetup('$basePath', [$config['basePath']]);

		$container->addDefinition($this->prefix('templateMailer'))
			->setClass('Smartsupp\Mailer\Mailer', [$messageFactory, $config['mailer']])
			->addSetup('$enabled', [$config['enabled']])
			->addSetup('setEmails', [$config['emails']]);
	}

}
