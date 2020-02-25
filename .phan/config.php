<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [

    // Supported values: `'5.6'`, `'7.0'`, `'7.1'`, `'7.2'`, `'7.3'`, `'7.4'`, `null`.
    // If this is set to `null`,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute Phan.
    "target_php_version" => '7.4',

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'src/',
        'tests/',
        'vendor/',
    ],

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to the `directory_list` as
    //       to `exclude_analysis_directory_list`.
    "exclude_analysis_directory_list" => [
        'vendor/'
    ],

    // A list of plugin files to execute.
    // See https://github.com/phan/phan/tree/master/.phan/plugins for even more.
    // (Pass these in as relative paths.
    // Base names without extensions such as 'AlwaysReturnPlugin'
    // can be used to refer to a plugin that is bundled with Phan)
    'plugins' => [
        'AlwaysReturnPlugin',
        'DuplicateArrayKeyPlugin',
        'PregRegexCheckerPlugin',
        'PrintfCheckerPlugin',
        'UnreachableCodePlugin',
        'InvokePHPNativeSyntaxCheckPlugin',
        'PHPUnitAssertionPlugin',
        'EmptyStatementListPlugin',
        'LoopVariableReusePlugin',
        'RedundantAssignmentPlugin',
        'PHPUnitNotDeadCodePlugin',
        'WhitespacePlugin',
        'PossiblyStaticMethodPlugin',
        'PHPDocRedundantPlugin',
    ],
    'plugin_config' => [
        // A list of 1 or more PHP binaries (Absolute path or program name found in $PATH)
        // to use to analyze your files with PHP's native `--syntax-check`.
        //
        // This can be used to simultaneously run PHP's syntax checks with multiple PHP versions.
        // e.g. `'plugin_config' => ['php_native_syntax_check_binaries' => ['php72', 'php70', 'php56']]`
        // if all of those programs can be found in $PATH

        // 'php_native_syntax_check_binaries' => [PHP_BINARY],

        // The maximum number of `php --syntax-check` processes to run at any point in time
        // (Minimum: 1. Default: 1).
        // This may be temporarily higher if php_native_syntax_check_binaries
        // has more elements than this process count.
        'php_native_syntax_check_max_processes' => 4,
    ],
    // Add any issue types (such as 'PhanUndeclaredMethod')
    // here to inhibit them from being reported
    'suppress_issue_types' => [
        'PhanPluginPossiblyStaticClosure',
    ],
];
