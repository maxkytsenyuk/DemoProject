<?php

namespace App\Event;

final class ItemEvents
{
    /**
     * @Event("App\Event\Item")
     */
    const NEW_ITEM = 'items.new_item';

    private function __construct()
    {
    }
}