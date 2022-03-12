<?php

namespace WebXID\BeBlogger\DataContainers;

class AbstractDataContainer
{
    protected $_data = [];

    #region Magic methods

    protected function __construct(array $data)
    {
        $this->_data = $data;
    }

    public function __get($name) {
        if (!isset($this->_data[$name]) && !property_exists($this, $name)) {
            return null;
        }

        return property_exists($this, $name)
            ? $this->$name
            : $this->_data[$name];
    }

    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }

    public function __isset($name) {
        return true;
    }

    public function __unset($name) {
        unset($this->_data[$name]);
    }

    #endregion

    #region Builders

    /**
     * @param array $data
     *
     * @return static
     */
    public static function make(array $data = []) {
        $object = new static($data);

        return $object;
    }

    #endregion
}
