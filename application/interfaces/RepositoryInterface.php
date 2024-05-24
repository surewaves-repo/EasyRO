<?php

namespace application\interfaces;

interface RepositoryInterface
{
    public function get();
    public function getColumnsWhere($conditions, $columns);
}
