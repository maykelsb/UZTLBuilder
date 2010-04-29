<?php
/**
* UZTLBuilder
* An app to build tiled layers for JavaME in PHP-GTK.
* Copyright (c) 2009-2010 Maykel "Gardner" dos Santos Braz <maykelsb@yahoo.com.br>
* -----------------------------------------------------------------------------
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License.
*
* The Original Code is
*   Maykel "Gardner" dos Santos Braz <maykelsb@yahoo.com.br>.
*
* The Initial Developer of the Original Code is
*   Maykel "Gardner" dos Santos Braz <maykelsb@yahoo.com.br>.
* Portions created by Initial Developer are Copyright (C) 2009-2010
* Initial Developer. All Rights Reserved.
*
* Contributor(s): None
*
* Alternatively, the contents of this file may be used under the terms
* of the New BSD license (the "New BSD License"), in which case the
* provisions of New BSD License are applicable instead of those
* above. If you wish to allow use of your version of this file only
* under the terms of the New BSD License and not to allow others to use
* your version of this file under the MPL, indicate your decision by
* deleting the provisions above and replace them with the notice and
* other provisions required by the New BSD License. If you do not delete
* the provisions above, a recipient may use your version of this file
* under either the MPL or the New BSD License.
*
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/

/**
* Classe de apoio para manipulação dos elementos da classe como arrays.
*
* @see Layer
* @see LayerLinha
*/
abstract class ArrayAccessIterator implements ArrayAccess, Iterator {
  protected $posicao = 0;
  protected $elementos = array();

  // -- ArrayAccess
  public function offsetExists($offset) { echo trigger_error('offsetExists não implementado!'); }
  public function offsetUnset($offset) { echo trigger_error('offsetUnset não implementado!'); }
  // -- Iterator
  public function current() { return $this->elementos[$this->posicao]; }
  public function next() { ++$this->posicao; }
  public function key() { return $this->posicao; }
  public function valid() { return array_key_exists($this->posicao, $this->elementos); }
  public function rewind() { $this->posicao = 0; }

  // -- Funções de manutenção dos elementos em $this->elementos
  public function array_slice($size, $offset = 0) {
    $this->elementos = array_slice($this->elementos, $offset, $size);
  }

  public function array_pad($size, $value = null) {
    if (is_array($value)) {
      $class = $value['class'];
      while (count($this->elementos) < $size) {
        $this->elementos[] = new $class($value['param']);
      }
    } else {
      $this->elementos = array_pad($this->elementos, $size, $value);
    }
  }

  public function count() { return count($this->elementos); }
}
?>
