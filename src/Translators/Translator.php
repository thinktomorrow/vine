<?php

namespace Vine\Translators;

interface Translator
{
    public function all(): array;

    public function key(): string;

    public function parentKey(): string;
}
