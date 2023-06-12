<?php

namespace http\Repositories;

interface SourceRepositoryInterface
{
    public function readDB($host, $user, $pass, $dbName);
}