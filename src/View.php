<?php


namespace Tiny\Skeleton;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Skeleton\Module\Core\Exception;

class View
{

    /**
     * @var array
     */
    private array $variables;

    /**
     * @var string|null
     */
    private ?string $templatePath;

    /**
     * @var string|null
     */
    private ?string $layoutPath;

    /**
     * @var string
     */
    private ?string $content;

    /**
     * View constructor.
     *
     * @param  array        $variables
     * @param  string|null  $templatePath
     * @param  string|null  $layoutPath
     */
    public function __construct(
        array $variables = [],
        string $templatePath = null,
        string $layoutPath = null
    ) {
        $this->variables = $variables;
        $this->templatePath = $templatePath;
        $this->layoutPath = $layoutPath;
    }

    /**
     * @return string|null
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param  string  $path
     *
     * @return $this
     */
    public function setTemplatePath(string $path): self
    {
        $this->templatePath = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLayoutPath()
    {
        return $this->layoutPath;
    }

    /**
     * @param  string  $path
     *
     * @return $this
     */
    public function setLayoutPath(string $path): self
    {
        $this->layoutPath = $path;

        return $this;
    }

    /**
     * @param  string  $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->templatePath) {
            throw new Exception\UnexpectedValueException(
                 'Template file path is empty.'
            );
        }

        $this->content = $this->render($this->templatePath);

        if ($this->layoutPath) {
            $this->content = $this->render($this->layoutPath);
        }

        return $this->content;
    }

    /**
     * @param  string  $filePath
     *
     * @return string
     */
    private function render(string $filePath): string
    {
        ob_start();
        require $filePath;

        return ob_get_clean() ?? '';
    }

}
