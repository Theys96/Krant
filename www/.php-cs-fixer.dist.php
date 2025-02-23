<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['app', 'views'])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;
