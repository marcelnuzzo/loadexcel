<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $noteMath;

    /**
     * @ORM\Column(type="integer")
     */
    private $noteFrancais;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNoteMath(): ?int
    {
        return $this->noteMath;
    }

    public function setNoteMath(int $noteMath): self
    {
        $this->noteMath = $noteMath;

        return $this;
    }

    public function getNoteFrancais(): ?int
    {
        return $this->noteFrancais;
    }

    public function setNoteFrancais(int $noteFrancais): self
    {
        $this->noteFrancais = $noteFrancais;

        return $this;
    }
}
