<?php

declare (strict_types=1);

namespace MultiTenancyBundle\Entity;

use MultiTenancyBundle\Repository\TenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TenantRepository::class)
 */
class Tenant implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\OneToMany(targetEntity=Hostname::class, mappedBy="tenant", orphanRemoval=true)
     */
    private $hostnames;

    public function __construct()
    {
        $this->hostnames = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return Collection|Hostname[]
     */
    public function getHostnames(): Collection
    {
        return $this->hostnames;
    }

    public function addHostname(Hostname $hostname): self
    {
        if (!$this->hostnames->contains($hostname)) {
            $this->hostnames[] = $hostname;
            $hostname->setTenant($this);
        }

        return $this;
    }

    public function removeHostname(Hostname $hostname): self
    {
        if ($this->hostnames->removeElement($hostname)) {
            // set the owning side to null (unless already changed)
            if ($hostname->getTenant() === $this) {
                $hostname->setTenant(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'uuid' => $this->getUuid(),
            'created_at' => $this->getCreatedAt(),
            'hostnames' => $this->getHostnames()->map(function ($object) {
                return $object->getFqdn();
            })
        ];
    }
}
