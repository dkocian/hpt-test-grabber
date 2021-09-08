<?php
declare(strict_types=1);

namespace HPT;

interface OutputInterface
{
    public function getJson(array $data): string;
}