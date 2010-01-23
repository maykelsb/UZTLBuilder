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
* Implementa métodos das interfaces ArrayAccess e Iterator.
*
* IMPORTANTE: Atualmente a classe suporta apenas chaves numéricas.
* @see ArrayAccess
* @see Iterator
* @abstract
*/
abstract class UpzArrayIterator implements ArrayAccess, Iterator {
  /**
  * Posição atual de acesso ao array quando utilizado como Iterator.
  * @var int $pos
  */
  private $pos = 0;
  /**
  * Array de armazenamento de elementos.
  * @var array $elementos
  */
  protected $elementos = array();

  // -- Implementações de ArrayAccess
  /**
  * Verifica se uma posição do array existe.
  * @param $offset int Posição para verificação.
  * @return boolean
  */
  public function offsetExists($offset) {
    return isset($this->elementos[$offset]);
  }
  /**
  * Retorna um elemento do array.
  * @param $offset int Posição de retorno.
  * @return mixed
  */
  public function offsetGet($offset) {
    return (isset($this->elementos[$offset])?$this->elementos[$offset]:null);
  }
  /**
  * Seta um elemento do array.
  *
  * Se não for informada uma posição, o novo elemento é inserido no final do array.
  * @param $offset int Posição de inserção.
  * @param $valor mixed Valor para o elemento.
  */
  public function offsetSet($offset, $valor) {
    if (is_null($offset)) {
      $offset = count($this->elementos);
    }
    $this->elementos[$offset] = $valor;
  }
  /**
  * Remove um elemento do array.
  * @param $offset int Posição do elemento para remoção.
  */
  public function offsetUnset($offset) {
    unset($this->elementos[$offset]);
  }

  // -- Implementações de Iterator
  /**
  * Retorna o elemento atual.
  * @return mixed
  */
  public function current() {
    return $this->elementos[$this->pos];
  }
  /**
  * Retorna a chave do elemento atual.
  * @return int
  */
  public function key() {
    return $this->pos;
  }
  /**
  * Avança a seleção para o próximo elemento.
  */
  public function next() {
    ++$this->pos;
  }
  public function rewind() {
    $this->pos = 0;
  }
  /**
  * Verifica se o elemento atual é válido.
  * @return boolean
  */
  public function valid() {
    return isset($this->elementos[$this->pos]);
  }
}
?>
