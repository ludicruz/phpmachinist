<?php
namespace DerpTest\Machinist;

class Relationship
{
    private $local_column;
    private $foreign_column;
    private $blueprint;
    private $type;

    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
    }

    public function local($key)
    {
        $this->local_column = $key;
        return $this;
    }

    public function type($type) {
        $this->type = $type;
        return $this;
    }

    public function foreign($key)
    {
        $this->foreign_column = $key;
        return $this;
    }

    public function getLocal()
    {
        return $this->local_column;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getForeign()
    {
        return $this->foreign_column;
    }

    public function getBlueprint()
    {
        return $this->blueprint;
    }

}
