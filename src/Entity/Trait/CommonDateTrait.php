<?php
namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait CommonDateTrait
{
    #[ORM\Column(type:"datetime_immutable")]
    #[Groups(
        [
            'brief:index', 'brief:show',
            'immersion:index', 'immersion:show',
            'projet:index', 'projet:show',
            'message:index', 'message:show',
            
        ]
    )]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type:"datetime_immutable")]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    Public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDate()
    {
        if($this->getCreatedAt() === null){
            $this->setCreatedAt(new \DateTimeImmutable() );
        }
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    // #[ORM\PrePersist]
    // public function onPrePersist(): void
    // {
    //     $this->updateDate();
    // }


}