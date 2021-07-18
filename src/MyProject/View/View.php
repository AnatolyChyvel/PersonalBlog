<?php

namespace MyProject\View;

class View
{
    private $templatesPath;

    private $extraVars = [];
    public function __construct(string $templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }
    /** adding new vars to the view
     */
    public function setVar(string $name, $value): void
    {
        $this->extraVars[$name] = $value;
    }

    public function renderHtml(string $templateName, array $vars = [], int $code = 200)
    {
        http_response_code($code);

        extract($this->extraVars);
        extract($vars);

        ob_start();
        include $this->templatesPath. '/' . $templateName;
        $buffer = ob_get_contents();
        ob_clean();

        echo $buffer;
    }
}