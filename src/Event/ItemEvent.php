<?php

namespace App\Event;

use App\Entity\Item;
use Symfony\Component\EventDispatcher\Event;

class ItemEvent extends Event
{
    /**
     * @var Item
     */
    protected $item;

    /**
     * ItemEvent constructor.
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     * @return ItemEvent
     */
    public function setItem(Item $item): ItemEvent
    {
        $this->item = $item;

        return $this;
    }
}