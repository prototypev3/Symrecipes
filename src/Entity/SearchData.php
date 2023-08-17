<?php

namespace App\Entity;

use App\Repository\SearchDataRepository;
use Doctrine\ORM\Mapping as ORM;

class SearchData
{

    private ?int $page = null;

    private ?string $q = null;

    private ?string $name = null;

    private ?int $id_user = null;


    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getQ(): ?string
    {
        return $this->q;
    }

    public function setQ(string $q): static
    {
        $this->q = $q;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
