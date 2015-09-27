<?php

namespace MoneyFlow\Category;

class Category
{
    /**
     * Category title
     *
     * @var string
     */
    protected $title;

    /**
     * Category title
     *
     * @var string
     */
    protected $description;

    /**
     * Category constructor
     *
     * @param string  $title
     * @param string  $description
     */
    public function __construct($title, $description)
    {
        $this->title       = $title;
        $this->description = $description;
    }

    /**
     * Returns category title
     *
     * @var string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns category description
     *
     * @var string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
