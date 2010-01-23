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
* Coleção de layers do projeto.
* @see ArrayIterator
* @final
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
final class Layers extends UpzArrayIterator {

  /**
  * Extenção dos arquivos de layer.
  * @const EXTENCAO_ARQUIVO_LAYER
  */
  const EXTENCAO_ARQUIVO_LAYER = 'upzlyr';

  /**
  *
  */
  public function offsetSet($offset, $valor) {
    if (get_class($valor) != 'Layer') {
      trigger_error('Tipo de elemento inválido. O novo valor deve ser uma instância de \'Layer\'.', E_USER_ERROR);
    }
    parent::offsetSet($offset, $valor);
  }

  /**
  * Referência para o projeto.
  * @see Projeto
  */
  private $projeto;

  /**
  * Carrega as layers do projeto ou as cria.
  *
  * Se os arquivos de layers já existirem, estes são carregadas, layer a layer,
  * caso não existam, são criados sem conteúdo.
  * @param $projeto Projeto Referência para o projeto.
  */
  public function __construct(Projeto $projeto) {
    $this->projeto = $projeto;
  }

  /**
  * Altera o nome de uma layer.
  * @param $pos int Posição da layer para alteração;
  * @param $nome string Novo nome para a layer.
  */
  public function renomearLayer($pos, $nome) {
    $this[$pos]->nome = $nome;
  }

  /**
  * Salva as layers do projeto.
  */
  public function salvarLayers() {
    foreach ($this as $layer) {
      $layer->salvarLayer($this->projeto->pathProjeto);
    }
  }

  /**
  * Cria as layers do projeto, sem nenhum conteúdo.
  * @see Layer::novaLayer
  */
  public function criarLayers() {
    $this->elementos = array_pad(
      $this->elementos,
      $this->projeto->quantidadeLayers,
      Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa));
  }

  public function carregarLayers() {
    $filtroArquivos = $this->projeto->pathProjeto . DIRECTORY_SEPARATOR . '*.' . Layers::EXTENCAO_ARQUIVO_LAYER;
    foreach (sort(glob($filtroArquivos)) as $arq) {
      $this[] = Layer::novaLayerDeXML($arq, $this->projeto->larguraLayer, $this->projeto->alturaLayer);
    }
  }

  public function adicionarLayer() {
    $this[] = Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa);
  }

  public function removerLayer($pos) {
    unset($this[$pos]);
  }
}
?>
