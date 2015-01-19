<?php

class object {

    // конструктор
    public function __construct($elems=null) {
        if($elems!=null) $this->setElements($elems);
    }

    // создаёт элементы объекта из существующего
    // объекта или массива
    final public function setElements($elems) {
        if(is_object($elems)) $elems = get_object_vars($elems);
        if(is_array($elems)) {
            foreach((array)$elems as $elem=>$value) {
                $this->set($elem, $value);
            }
        };
        return false;
    }

    // возвращает массив вида "Элемент"=значение
    //   $public_only - если true:  вернёт значиния за исключением скрытых
    //             если false: вернёт все значения
    // признак скрытых переменных - в начале имени знак подчёркивания _
    final public function getElements($public_only=true) {
        $rv = get_object_vars($this);

        if($public_only==true) {
            foreach($rv as $elem=>$val) {
                if(substr($elem, 0, 1)=="_") {
                    unset($rv[$elem]);
                }
            }
        }

        return $rv;
    }

    // устанавливает значение элементу объекта
    //   $elem - имя элемента
    //   $value - значение элемента
    // функция возвращает предыдущее значение элемента
    // если элемента не было, возвращается null
    final public function set($elem, $value=null) {
        if($elem==null) return null;
        $rv = isset($this->$elem)?$this->$elem:null;
        $this->$elem = $value;
        return $rv;
    }

    // получить значение элемента объекта
    // если элемент не существует, то создаёт его со значением по умолчанию $default
    // и возвращает это значение по умолчанию
    final public function get($elem, $default=null) {
        if($elem==null) return null;
        if(isset($this->$elem)) {
            return $this->$elem;
        }

        $this->set($elem, $default);
        return $default;
    }

    // Удаляем элемент из объекта
    public function del($elem) {
        if(isset($this->$elem)) unset($this->$elem);
    }

    // Клонирует значение элемента в другой элеменет
    public function cln($fromelem, $toelem, $default=null) {
        $value = $this->get($fromelem, $default);
        $this->set($toelem, $value);
        return $value;
    }

    // Переименовывает элемент
    public function rename($oldName, $newName) {
        $this->set($newName, $this->get($oldName));
        $this->del($oldName);
    }

    // Возвращает имя класса
    public function __toString() {
        return get_class($this);
    }
};

?>
