<?php

declare(strict_types=1);

namespace Smartsupp\Mailer\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Smartsupp\Mailer\DisabledMailer;
use Smartsupp\Mailer\ITemplateFactory;
use Smartsupp\Mailer\ITemplateMailer;
use Smartsupp\Mailer\ITemplateRenderer;
use Smartsupp\Mailer\LegacyTemplateRenderer;
use Smartsupp\Mailer\TemplateRendererSelector;
use Smartsupp\Mailer\TemplateFactory;
use Smartsupp\Mailer\TemplateMailer;

class MailerExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'mailer' => Expect::mixed('@mail.mailer'),
            'enabled' => Expect::bool(true),
            'emails' => Expect::array(),
            'basePath' => Expect::string(),
            'templatesDir' => Expect::string()->required(),
            'params' => Expect::array(),
            'defaultRenderer' => Expect::mixed('@' . $this->prefix('legacyRenderer')),
            'renderers' => Expect::array(),
            'templateRenderers' => Expect::arrayOf(Expect::arrayOf(Expect::string())),
        ]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('templateFactory'))
            ->setType(ITemplateFactory::class)
            ->setFactory(TemplateFactory::class)
            ->addSetup('setDefaultParameters', [$this->config->params])
            ->addSetup('$templatesDir', [$this->config->templatesDir]);

        $legacyRendererDefinition = $container->addDefinition($this->prefix('legacyRenderer'))
            ->setType(ITemplateRenderer::class)
            ->setFactory(LegacyTemplateRenderer::class)
            ->setAutowired(false);

        $rendererDefinition = $container->addDefinition($this->prefix('renderer'))
            ->setType(ITemplateRenderer::class);

        $renderers = $this->config->renderers ?? [];
        if (\count($renderers) === 0) {
            $rendererDefinition->setFactory($legacyRendererDefinition);
        } else if (\count($renderers) === 1 && !isset($this->config->defaultRenderer)) {
            $rendererDefinition->setFactory(\reset($renderers));
        } else {
            $rendererDefinition->setFactory(TemplateRendererSelector::class . '::create')
                ->setArgument('renderers', $renderers)
                ->setArgument('templates', $this->config->templateRenderers ?? [])
                ->setArgument('defaultRenderer', $this->config->defaultRenderer ?? null);
        }

        $mailerDefinition = $container->addDefinition($this->prefix('mailer'));
        $mailerDefinition->setType(ITemplateMailer::class);
        if ($this->config->enabled ?? false) {
            $mailerDefinition->setFactory(TemplateMailer::class);
            $mailerDefinition->setArgument('emails', $this->config->emails ?? []);
            $mailerDefinition->setArgument('basePath', $this->config->basePath ?? null);
        } else {
            $mailerDefinition->setFactory(DisabledMailer::class);
        }
    }
}
