<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    protected $canAddItems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="user_id")
     */
    private $items;

    public function __construct()
    {
        parent::__construct();
        $this->items = new ArrayCollection();
        // your own logic
    }

    /**
     * @return bool
     */
    public function getCanAddItems()
    {
        return $this->canAddItems;
    }

    /**
     * @param bool $canAddItems
     * @return $this
     */
    public function setCanAddItems(bool $canAddItems)
    {
        $this->canAddItems = $canAddItems;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param Item $item
     * @return User
     */
    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setUserId($this);
        }

        return $this;
    }

    /**
     * @param Item $item
     * @return User
     */
    public function removeItem(Item $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getUserId() === $this) {
                $item->setUserId(null);
            }
        }

        return $this;
    }
}