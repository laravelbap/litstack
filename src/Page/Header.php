<?php

namespace Fjord\Page;

use Fjord\Support\VueProp;

class Header extends VueProp
{
    /**
     * Header title.
     *
     * @var string
     */
    protected $title;

    /**
     * Slot "left".
     *
     * @var Slot
     */
    protected $left;

    /**
     * Slot "right".
     *
     * @var Slot
     */
    protected $right;

    /**
     * Create new Navigation instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->left = new Slot;
        $this->right = new Slot;
    }

    /**
     * Get slot right.
     *
     * @return Slot
     */
    public function getRightSlot()
    {
        return $this->right;
    }

    /**
     * Get slot left.
     *
     * @return Slot
     */
    public function getLeftSlot()
    {
        return $this->left;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param  string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Render slot for Vue.
     *
     * @return array
     */
    public function render(): array
    {
        return [
            'left'  => $this->left,
            'right' => $this->right,
            'title' => $this->title,
        ];
    }
}