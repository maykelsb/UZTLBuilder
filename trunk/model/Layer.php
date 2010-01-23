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
* Cole��o de linhas de tiles de uma layer.
* @see ArrayIterator
* @final
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
final class Layer extends UpzArrayIterator {

  /**
  *
  */
  private $propriedades = array(
    'id' => null,
    'nome' => null,
    'larguraLayer' => null,
    'alturaLayer' => null);

  /**
  *
  */
  public function offsetSet($offset, $valor) {
    if (get_class($valor) != 'LayerLinha') {
      trigger_error('Tipo de elemento inv�lido. O novo valor deve ser uma inst�ncia de \'LinhaLayer\'.', E_USER_ERROR);
    }
    parent::offsetSet($offset, $valor);
  }

  public function __get($prop) {
    if (array_key_exists($prop, $this->propriedades)) {
      return $this->propriedades[$prop];
    }
    trigger_error("Propriedade ({$prop}) da layer n�o definida!", E_USER_ERROR);
  }

  public function __set($prop, $valor) {
    if (array_key_exists($prop, $this->propriedades)) {
      $this->propriedades[$prop] = $valor;
      return;
    }
    trigger_error("Propriedade ({$prop}) da layer n�o definida!", E_USER_ERROR);
  }

  /**
  *
  */
  private function __construct($larguraLayer, $alturaLayer) {
    $this->larguraLayer = $larguraLayer;
    $this->alturaLayer = $alturaLayer;
    // -- Criando as linhas de cada camada
    for ($y = 0; $y < $this->alturaLayer; $y++) {
      $this[] = new LayerLinha($larguraLayer);
    }
  }

  /**
  * @static
  */
  public static function novaLayer($larguraLayer, $alturaLayer) {
    return new Layer($larguraLayer, $alturaLayer);
  }

  /**
  * @static
  */
  public static function novaLayerDeXML($pathXMLDaLayer, $larguraLayer, $alturaLayer) {
    if (!($spXML = simplexml_load_file($pathXMLDaLayer))) {
      trigger_error('N�o foi poss�vel carregar as defini��es da layer.', E_USER_ERROR);
    }
    $layer = new Layer($larguraLayer, $alturaLayer);
    // -- Atributos da layer
    foreach ($spXML->attributes as $key => $valor) { $layer->$key = (string)$valor; }
    // -- Carregando linhas da layer
    foreach ($spXML->children() as $key => $valor) {
      $lnhLayer = new LinhaLayer($larguraLayer);
      $lnhLayer->carregarLinha($valor);
      $layer[] = $lnhLayer;
    }
    return $layer;
  }

  public function salvarLayer($path) {
    $domDoc = new DomDocument('1.0', 'iso-8859-1');
    $elmRoot = $domDoc->appendChild(new DomElement('layer'));
    $elmRoot->setAttributeNode(new DOMAttr('id', $this->id));
    $elmRoot->setAttributeNode(new DOMAttr('nome', $this->nome));
    foreach ($this as $keyRow => $row) {
      $rowTile = new DomElement('row');
      $rowTile->setAttributeNode(new DOMAttr('num', $keyRow));
      $elmRoot->appendChild($rowTile);
      foreach ($row as $keyCol => $col) {
        $colTile = new DomElement('col', (string)$col);
        $colTile->setAttributeNode(new DOMAttr('num', $keyCol));
        $rowTile->appendChild($colTile);
      }
    }
    $pathLayer = $path . sprintf("%02d", $this->id) . Layers::EXTENCAO_ARQUIVO_LAYER;
    if (false !== file_put_contents($pathLayer, $domDoc->saveXML())) {
      trigger_error('N�o foi poss�vel salvar defini��es de layer', E_USER_ERROR);
    }
  }

  private function ajustarDimensoesLayer() {
    $larguraAtual = count($this[0]);
    $alturaAtual = count($this);

    // -- A ordena��o dos ajustes visa uma melhor performance, sem desperdi�ar a��es
    if ($this->alturaLayer < $alturaAtual) {
      // -- Remove linhas do final
      $this->elementos = array_slice($this->elementos, 0, $this->alturaLayer);
    }
    if ($this->larguraLayer < $larguraAtual) {
      // -- Remove colunas do final de todas as linhas
      foreach ($this as &$linha) {
        $linha->diminuirLargura($this->larguraLayer, $larguraAtual);
      }
    }
    if ($this->larguraLayer > $larguraAtual) {
      // -- Adiciona colunas no final de todas as linhas
      foreach ($this as &$linha) {
        $linha->aumentarLargura($this->larguraLayer, $larguraAtual);
      }
    }
    if ($this->alturaLayer > $alturaAtual) {
      // -- Adiciona novas linhas no final da layer j� com o tamanho correto
      $this->elementos = array_pad(
        $this->elementos,
        $this->alturaLayer,
        new LayerLinha($this->larguraLayer));
    }
  }
}
?>
