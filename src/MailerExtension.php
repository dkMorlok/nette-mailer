<?php

namespace Smartsupp\Mailer;

use Nette\DI\CompilerExtension;

class MailerExtension extends CompilerExtension
{

	public $defaults = array(
		'enabled' => true,
		'mailer' => null,
		'emails' => array(),
		'basePath' => null,
		'templatesDir' => null,
		'params' => array()
	);


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$container->addDefinition($this->prefix('sender'))
			->setClass('Smartsupp\Mailer\Sender', array($config['mailer']))
			->addSetup('setEmails', array($config['emails']))
			->addSetup('$catchExceptions', array(!$container->parameters['debugMode']));

		$container->addDefinition($this->prefix('templateFactory'))
			->setClass('Smartsupp\Mailer\TemplateFactory')
			->addSetup('setDefaultParameters', array($config['params']))
			->addSetup('$templatesDir', array($config['templatesDir']));

		$container->addDefinition($this->prefix('messageFactory'))
			->setClass('Smartsupp\Mailer\TemplateMessageFactory')
			->addSetup('$basePath', array($config['basePath']));

		$container->addDefinition($this->prefix('mailer'))
			->setClass('Smartsupp\Mailer\Mailer')
			->addSetup('$enabled', array($config['enabled']));
	}

}
