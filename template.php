<?php

class Template {
    protected $variables = [];
    protected $loops = [];
    protected $templatePath;
    protected $templateContent;

    public function __construct($config) {
        $this->templatePath = rtrim($config['root'], '/') . '/';
    }

    public function vars($varsArray) {
        foreach ($varsArray as $key => $value) {
            $this->variables[$key] = $value;
        }
    }

    public function loop($name, $data) {
        if (!isset($this->loops[$name])) {
            $this->loops[$name] = [];
        }
        $this->loops[$name][] = $data;
    }

    public function display($pageTitle, $templateName, $cssPath = null, $jsPath = null) {
        $this->templateContent = file_get_contents($this->templatePath . $templateName . '.html');

        // Replace static variables
        foreach ($this->variables as $key => $value) {
            $this->templateContent = str_replace("{{$key}}", $value, $this->templateContent);
        }

        // Process loops
        foreach ($this->loops as $loopName => $loopData) {
            if (empty($loopData)) {
                $this->templateContent = preg_replace("/<!-- LOOP $loopName -->.*?<!-- LOOPELSE $loopName -->(.*?)<!-- END $loopName -->/s", '$1', $this->templateContent);
            } else {
                $loopContent = '';
                $pattern = "/<!-- LOOP $loopName -->(.*?)<!-- END $loopName -->/s";
                preg_match($pattern, $this->templateContent, $matches);
                foreach ($loopData as $item) {
                    $itemContent = $matches[1];
                    foreach ($item as $key => $value) { 
                        $loopNameKey = $loopName.".".$key;
                        $itemContent = str_replace("{{$loopNameKey}}", $value, $itemContent);
                    }
                    $loopContent .= $itemContent;
                }
                $this->templateContent = preg_replace($pattern, $loopContent, $this->templateContent);
            }
        }

        echo $this->templateContent;
    }
}

?>
