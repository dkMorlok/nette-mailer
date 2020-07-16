<?php

declare(strict_types=1);

namespace Smartsupp\Mailer;

use Latte;
use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;

class Template implements ITemplate
{

    /** @var Latte\Engine */
    private $latte;

    /** @var string */
    private $file;

    /** @var array */
    private $params = [];

    /** @var array */
    private $filters = [];


    public function __construct(Latte\Engine $latte)
    {
        $this->latte = $latte;
    }


    /**
     * Sets the path to the template file.
     * @return static
     */
    public function setFile(string $file)
    {
        $this->file = $file;
        return $this;
    }


    /**
     * Returns the path to the template file.
     */
    public function getFile(): ?string
    {
        return $this->file;
    }


    /**
     * Renders template to output.
     */
    public function render(): void
    {
        $this->latte->render($this->file, $this->params);
    }


    /**
     * Renders template to string.
     */
    public function renderToString(): string
    {
        $string = $this->latte->renderToString($this->file, $this->params);
        foreach ($this->filters as $filter) {
            $string = $filter($string);
        }
        return $string;
    }


    /**
     * Registers run-time filter.
     */
    public function addFilter(?string $name, callable $callback): void
    {
        $this->latte->addFilter($name, $callback);
    }


    /**
     * Registers after render filter.
     */
    public function addAfterFilter(callable $callback): void
    {
        $this->filters[] = $callback;
    }


    /**
     * Sets all parameters.
     */
    public function setParameters(array $params): void
    {
        $this->params = $params + $this->params;
    }


    /**
     * Sets translate adapter.
     */
    public function setTranslator(?ITranslator $translator = null): void
    {
        $this->latte->addFilter('translate', $translator === null ? null : [$translator, 'translate']);
    }
}
