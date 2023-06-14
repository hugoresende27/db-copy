<?php

namespace App\http\Repositories;

interface SourceRepositoryInterface
{
    public function readDB($host, $user, $pass, $dbName);
}