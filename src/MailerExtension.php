<?php declare(strict_types = 1);

namespace Smartsupp\Mailer;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class MailerExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'mailer' => Expect::mixed('@mail.mailer'),
			'enabled' => Expect::bool(true),
			'emails' => Expect::array(),
			'basePath' => Expect::string(),
			'templatesDir' => Expect::string(),
			'params' => Expect::array(),
		]);
	}


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('templateFactory'))
			->setFactory(TemplateFactory::class)
			->addSetup('setDefaultParameters', [$this->config->params])
			->addSetup('$templatesDir', [$this->config->templatesDir]);

		$messageFactory = $container->addDefinition($this->prefix('messageFactory'))
			->setFactory(TemplateMessageFactory::class)
			->addSetup('$basePath', [$this->config->basePath]);

		$container->addDefinition($this->prefix('templateMailer'))
			->setFactory(Mailer::class, [$messageFactory, $this->config->mailer])
			->addSetup('$enabled', [$this->config->enabled])
			->addSetup('setEmails', [$this->config->emails]);
	}

}
