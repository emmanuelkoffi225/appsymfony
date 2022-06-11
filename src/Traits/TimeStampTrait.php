<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TimeStampTrait
{

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updateAt;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }


    #[ORM\PrePersist()]
    public function onPrePersist(){
        $this->createdAt = New \DateTime();
        $this->updateAt = New \DateTime();
    }

    #[ORM\PreUpdate()]
    public function onPreUpdate(){
        $this->updateAt = New \DateTime();
    }

}