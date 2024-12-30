<?php

$finder = (new PhpCsFixer\Finder())
    ->in('www')
    ->exclude([
        'www/classes/Jfcherng',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;
