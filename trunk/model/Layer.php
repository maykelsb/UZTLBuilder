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
* Implementa uma coleção de linhas de tiles que compõem uma layer.
*
* @property int $larguraLayer Largura da layer em tiles;
* @property int $alturaLayer Altura da layer em tiles;
* @final
* @see ArrayAccessIterator
* @see LayerLinha
*/
class Layer extends ArrayAccessIterator {

  /**
  * Extenção dos arquivos do conteúdo das layers do projeto.
  * @const EXTENCAO_ARQUIVO_LAYER
  */
  const EXTENCAO_ARQUIVO_LAYER = 'uzl';

  private $larguraLayer;
  private $alturaLayer;
  private $id;
  private $nome;

  // -- Getters and Setters
  public function setDimensoes($largura, $altura) {
    $this->alturaLayer = $altura;
    $this->larguraLayer = $largura;
  }

  public function getDimensoes() {
    return array($this->alturaLayer, $this->larguraLayer);
  }

  // -- Construtores
  private function __construct() {}

  public static function criarLayer($largura, $altura) {
    $layer = new Layer();
    $layer->setDimensoes($largura, $altura);
    while($altura--) { $layer[] = new LayerLinha($largura); }
    return $layer;
  }

  public static function carregarLayer($pathXML) {
    if (!($oSpXML = simplexml_load_file($pathXML))) {
      trigger_error('Não foi possível carregar as definições da layer.', E_USER_ERROR);
    }
    $layer = new Layer();
    foreach ($oSpXML->children() as $key => $mValor) {
      $layerlinha = new LayerLinha();
      $layerlinha->carregarLinha($mValor);
      $layer[] = $layerlinha;
    }
    return $layer;
  }

  // -- Métodos de manutenção
  public function ajustarDimensoesLayer() {
    $larguraAtual = $this[0]->count();
    $alturaAtual = $this->count();
    // -- A ordenação dos ajustes visa uma melhor performance, sem desperdiçar ações
    if ($this->alturaLayer < $alturaAtual) { // -- Remove linhas do final
      $this->array_slice($this->alturaLayer);
    }
    if ($this->larguraLayer < $larguraAtual) {
      // -- Remove colunas do final de todas as linhas
      foreach ($this as $key => $linha) {
        $this[$key]->array_slice($this->larguraLayer);
      }
    }
    if ($this->larguraLayer > $larguraAtual) {
      // -- Adiciona colunas no final de todas as linhas
      foreach ($this as $key => $linha) {
        $this[$key]->array_pad($this->larguraLayer);
      }
    }
    if ($this->alturaLayer > $alturaAtual) {
      // -- Adiciona novas linhas no final da layer já com o tamanho correto
      $this->array_pad($this->alturaLayer, array('class' => 'LayerLinha',
                                                 'param' => $this->larguraLayer));
    }
  }

  // -- Método de persistência
  public function salvarLayer($path, $id) {
    $this->id = $id;
    $domDoc = new DomDocument('1.0', 'iso-8859-1');
    $elmRoot = $domDoc->appendChild(new DomElement('layer'));
    $elmRoot->setAttributeNode(new DOMAttr('id', $this->id));
    $elmRoot->setAttributeNode(new DOMAttr('nome', $this->nome));

    foreach ($this as $rowKey => $row) {
      $rowTile = $elmRoot->appendChild(new DomElement('row'));
      foreach ($row as $col) {
        $colTile = $rowTile->appendChild(new DomElement('col', $col));
      }
    }

    // -- Salvando arquivo de conteúdo da layer
    $pathLayer = $path . DIRECTORY_SEPARATOR . sprintf("%02d", $this->id) . '.' . self::EXTENCAO_ARQUIVO_LAYER;
    if (false === file_put_contents($pathLayer, $domDoc->saveXML())) {
      trigger_error('Não foi possível salvar definições de layer', E_USER_ERROR);
    }
  }

  // -- ArrayIterator
  public function offsetGet($offset) {
    if (isset($offset)) { return $this->elementos[$offset]; }
    return $this->elementos[count($this->elementos) - 1];
  }

  public function offsetSet($offset, $value) {
    if ($value instanceof LayerLinha) {
      if (is_null($offset)) { $this->elementos[] = $value;
      } else { $this->elementos[$offset] = $value; }
    }
  }
}
?>
