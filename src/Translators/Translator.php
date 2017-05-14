<?php

namespace Vine\Translators;

interface Translator
{
    function all(): array;

    function key(): string;

    function parentKey(): string;
}