<?php

class Template {
    protected $variables = [];
    protected $loops = [];
    protected $templatePath;
    protected $templateContent = '';

    public function __construct($config) {
        $this->templatePath = rtrim($config['root'], '/') . '/';
    }

    public function vars($varsArray) {
        foreach ($varsArray as $key => $value) {
            $this->variables[$key] = $value;
        }
    }

    public function loop($name, array $data) {
        if (!isset($this->loops[$name])) {
            $this->loops[$name] = [];
        }
        $this->loops[$name][] = $data;
    }

    public function display($pageTitle, $templateName) {
        $templateFilePath = $this->templatePath . $templateName . '.html';

        if (!file_exists($templateFilePath) || !is_readable($templateFilePath)) {
            echo "Error: Template file not found or not readable.";
            return;
        }

        $this->templateContent = file_get_contents($templateFilePath);

        // Replace static variables
        foreach ($this->variables as $key => $value) {
            $this->templateContent = str_replace("{{$key}}", htmlspecialchars($value), $this->templateContent);
        }

        // Process loops
        foreach ($this->loops as $loopName => $loopData) {
            $loopContent = '';
            $pattern = "/<!-- LOOP $loopName -->(.*?)<!-- END $loopName -->/s";

            if (preg_match($pattern, $this->templateContent, $matches) && isset($matches[1])) {
                $loopTemplate = $matches[1];

                foreach ($loopData as $item) {
                    $itemContent = $loopTemplate;
                    foreach ($item as $key => $value) {
                      $loopNameKey = $loopName.".".$key;
                        $itemContent = str_replace("{{$loopNameKey}}", htmlspecialchars($value), $itemContent);
                    }
                    $loopContent .= $itemContent;
                }
            }

            if (empty($loopData)) {
                $this->templateContent = preg_replace("/<!-- LOOP $loopName -->.*?<!-- LOOPELSE $loopName -->(.*?)<!-- END $loopName -->/s", '$1', $this->templateContent);
            } else {
                $this->templateContent = preg_replace($pattern, $loopContent, $this->templateContent);
            }
        }

        echo $this->templateContent;
    }
}

?>
