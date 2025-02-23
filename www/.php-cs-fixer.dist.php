<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_to_comment' => false,
    ])
    ->setFinder(
         (new PhpCsFixer\Finder())
            ->in(['app', 'views'])
    )
;
