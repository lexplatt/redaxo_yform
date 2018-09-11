<?php

class rex_yform_manager_field implements ArrayAccess
{
    protected $values = [];
    protected $definitions = [];
    protected static $debug = false;
    protected static $types = ['value', 'validate', 'action'];

    public function __construct(array $values)
    {
        $class = 'rex_yform_' . $values['type_id'] . '_' . $values['type_name'];

        if (count($values) == 0 || !class_exists($class)) {
            throw new Exception(rex_i18n::msg('yform_field_not_found'));
        }

        $this->object = new $class();
        $this->definitions = $this->object->getDefinitions($values);
        if (isset($this->definitions['values'])) {
            $i = 'validate' === $values['type_id'] ? 2 : 1;
            foreach ($this->definitions['values'] as $key => $value) {
                if (isset($values['f' . $i]) && (!isset($values[$key]) || null === $values[$key] || '' === $values[$key])) {
                    $values[$key] = $values['f' . $i];
                }
                ++$i;
            }
        }
        $this->values = $values;
    }

    public static function table()
    {
        return rex::getTablePrefix() . 'yform_field';
    }

    // value, validate, action
    public function getType()
    {
        $type_id = $this->values['type_id'];
        if (!in_array($type_id, self::$types)) {
            return false;
        }
        return $type_id;
    }

    // rex_yform_select
    public function getTypeName()
    {
        if (!isset($this->values['type_name'])) {
            return '';
        }
        return $this->values['type_name'];
    }

    public function getName()
    {
        return $this->values['name'];
    }

    public function getLabel()
    {
        return rex_i18n::translate($this->values['label']);
    }

    public function getElement($k)
    {
        if (!isset($this->values[$k])) {
            return null;
        }
        return $this->values[$k];
    }

    public function getDatabaseFieldType()
    {
        if (!isset($this->values['db_type']) || $this->values['db_type'] == '') {
            return $this->object->getDatabaseFieldDefaultType();
        }
        return $this->values['db_type'];
    }

    public function getDatabaseFieldTypes()
    {
        return $this->object->getDatabaseFieldTypes();
    }

    public function getDatabaseFieldDefaultType()
    {
        return $this->object->getDatabaseFieldDefaultType();
    }

    public function getDatabaseFieldNull()
    {
        return $this->object->getDatabaseFieldNull();
    }

    public function getHooks()
    {
        return (isset($this->definitions['hooks'])) ? $this->definitions['hooks'] : [];
    }

    public function isSearchableDisabled()
    {
        if (!isset($this->definitions['is_searchable'])) {
            return false;
        }
        if (!$this->definitions['is_searchable']) {
            return true;
        }
        return false;
    }

    public function isSearchable()
    {
        if ($this->isSearchableDisabled()) {
            return false;
        }

        if (isset($this->values['search']) && $this->values['search']) {
            return true;
        }
        return false;
    }

    public function isHiddenInListDisabled()
    {
        if (!isset($this->definitions['is_hiddeninlist'])) {
            return false;
        }
        if ($this->definitions['is_hiddeninlist']) {
            return true;
        }
        return false;
    }

    public function isHiddenInList()
    {
        if ($this->isHiddenInListDisabled()) {
            return true;
        }

        if (!isset($this->values['list_hidden']) || $this->values['list_hidden']) {
            return true;
        }
        return false;
    }

    // deprecated
    // sobald die yform value klassen umgebaut worden sind.
    public function toArray()
    {
        return $this->values;
    }

    // ------------------------------------------- Array Access
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function __toString()
    {
        return $this->getName();
    }
}
