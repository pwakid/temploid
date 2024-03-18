<?php
require_once("template.php");
$config = ['root' => '/path/to/templates'];

$template = new Template($config);

$template->vars([
    'site_name' => 'My Website',
    'page_title' => 'Welcome!'
]);

$categories = [
    ['name' => 'Category 1'],
    ['name' => 'Category 2']
];

foreach ($categories as $category) {
    $template->loop('category', $category);
}

$template->display('Page Title', 'example.html');
?>
