<?php

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
        'mate/extensions.php',
        'mate/config.php',
    ])
;

return new PhpCsFixer\Config()
    ->setRules([
        '@Symfony' => true,
        'control_structure_continuation_position' => ['position' => 'next_line'],
        'declare_strict_types' => true,
        'no_unreachable_default_argument_value' => false,
        'yoda_style' => ['equal' => null, 'identical' => null, 'less_and_greater' => null],
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'none'],
        'blank_line_after_opening_tag' => false,
        'not_operator_with_successor_space' => true,

    ])
    ->setFinder($finder)
    ->setCacheFile('/tmp/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
;
