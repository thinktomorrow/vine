<?php

namespace Tests\Fixtures;

use Vine\Translators\Translator;

/**
 * User: bencavens
 */
class FixtureTranslator implements Translator
{
    private $flatten;

    public function __construct($type = 'default')
    {
        $filename = 'dataFixture.php';

        if($type == 'large')
        {
            $filename = 'largeDataFixture.php';
        }

        $this->flatten = require __DIR__.'/'.$filename;
    }

    function all(): array
    {
        return $this->flatten;
    }

    function key(): string
    {
        return 0;
    }

    function parentKey(): string
    {
        return 1;
    }
}