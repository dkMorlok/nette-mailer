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


	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$container->addDefinition($this->prefix('templateFactory'))
			->setType(\Smartsupp\Mailer\ITemplateFactory::class)
			->setFactory(\Smartsupp\Mailer\TemplateFactory::class)
			->addSetup('setDefaultParameters', [$config['params']])
			->addSetup('$templatesDir', [$config['templatesDir']]);

		$messageFactory = $container->addDefinition($this->prefix('messageFactory'))
			->setType(\Smartsupp\Mailer\ITemplateMessageFactory::class)
			->setFactory(\Smartsupp\Mailer\TemplateMessageFactory::class)
			->addSetup('$basePath', [$config['basePath']]);

		$container->addDefinition($this->prefix('templateMailer'))
			->setFactory(\Smartsupp\Mailer\Mailer::class, [$messageFactory, $config['mailer']])
			->addSetup('$enabled', [$config['enabled']])
			->addSetup('setEmails', [$config['emails']]);
	}

}
