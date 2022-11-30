<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abonnement
 *
 * @ORM\Table(name="abonnement", indexes={@ORM\Index(name="id_associe", columns={"id_associe"}), @ORM\Index(name="id_promo", columns={"id_promo"})})
 * @ORM\Entity(repositoryClass="App\Repository\AbonnementRepository")
 */
class Abonnement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_promo", type="integer", nullable=false)
     */
    private $idPromo;

    /**
     * @var int
     *
     * @ORM\Column(name="id_associe", type="integer", nullable=false)
     */
    private $idAssocie;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="nRating", type="integer", nullable=false)
     */
    private $nrating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPromo(): ?int
    {
        return $this->idPromo;
    }

    public function setIdPromo(int $idPromo): self
    {
        $this->idPromo = $idPromo;

        return $this;
    }

    public function getIdAssocie(): ?int
    {
        return $this->idAssocie;
    }

    public function setIdAssocie(int $idAssocie): self
    {
        $this->idAssocie = $idAssocie;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getNrating(): ?int
    {
        return $this->nrating;
    }

    public function setNrating(int $nrating): self
    {
        $this->nrating = $nrating;

        return $this;
    }


}
