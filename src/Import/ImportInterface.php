<?php

namespace Integreat\Gemeindeverzeichnis\Import;

interface ImportInterface
{
    public function getName() : string;

    public function getPriority() : int;

    public function import();
}