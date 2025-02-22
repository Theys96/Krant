<?php

$finder = (new PhpCsFixer\Finder())
    ->in('classes')
    ->exclude([
        'classes/App/Jfcherng',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;
