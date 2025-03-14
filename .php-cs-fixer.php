<?php
$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['vendor']); // Exclude unnecessary directories

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // Enforce PSR-12 coding standard
        '@Symfony' => true, // PHP Extended Rules (improves consistency)
        'yoda_style' => false, // Prevent Yoda conditions
        'concat_space' => ['spacing' => 'one'], // Adds a space around concatenation
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(false) // Prevent risky modifications for now
    ->setLineEnding("\n"); // Normalize line endings
