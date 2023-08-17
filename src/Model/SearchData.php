<?php

namespace App\Model;

class SearchData
{
    /** @var int */
    private  $page = 1;

    private string $name = '';

    private string $q = '';


    public function getQ(): string
    {
        return $this->q;
    }

    public function setQ(string $q): self
    {
        $this->q = $q;
        return $this;
    }
}
