<?php

use Adianti\Widget\Form\TEntry;

class MTEntry extends TEntry
{
    public function setEditable($editable)
    {
        parent::setEditable($editable);
        if (!$editable)
        {
            $this->tag->tabindex = '-1';
        }
    }
}