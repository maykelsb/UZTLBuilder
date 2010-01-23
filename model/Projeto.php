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
* Classe de persist�ncia de projetos.
*
* Faz o controle de persist�ncia dos projetos como: criar, carregar, abrir e salvar.
* Tamb�m define informa��es sobre tipo de arquivo.
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
* @final
*/
final class Projeto {

  /**
  * Refer�ncia para inst�ncia desta classe.
  * @see Projeto
  */
  private static $projeto = null;

  /**
  * Exten��o utilizada pelos arquivos de defini��o de projetos.
  * @const EXTENCAO_ARQUIVO_PROJETO
  */
  const EXTENCAO_ARQUIVO_PROJETO = 'utbp';
  /**
  * Exten��o utilizada pelos arquivos de layers.
  * @const EXTENCAO_ARQUIVO_LAYER
  */
  const EXTENCAO_ARQUIVO_LAYER = 'upzlyr';
  /**
  * Descri��o do tipo de arquivo de projeto.
  * @const DESC_TIPO_ARQUIVO_PROJETO
  */
  const DESC_TIPO_ARQUIVO_PROJETO = 'Projeto UZTLBuilder';
  /**
  * Nome do diret�rio de tiles dentro da pasta de trabalho do projeto.
  * @const PATH_TILES
  */
  const PATH_TILES = 'tiles';

  /**
  *
  */
  private $propriedades = array(
    'pathProjeto' => null,
    'dataCriacao' => null,
    'larguraTile' => null,
    'alturaTile' => null,
    'larguraMapa' => null,
    'alturaMapa' => null,
    'quantidadeLayers' => null,
    'corDeFundo' => null,
    'pathTileset' => null,
    'linguagemExport' => null,
    'layers' => null);

  /**
  * Retorna as propriedades da classe.
  * @param $prop String Nome da propriedade da classe.
  */
  public function __get($prop) {
    if (!array_key_exists($prop, $this->propriedades)) {
      trigger_error("Propriedade ({$prop}) do projeto n�o definida!", E_USER_ERROR);
    }
    return $this->propriedades[$prop];
  }

  /**
  * Define um valor para as propriedade das clase.
  * @param $prop String Nome da propriedade da classe.
  * @param $valor mixed Novo valor para a propriedade da classe.
  */
  public function __set($prop, $valor) {
    if (!array_key_exists($prop, $this->propriedades)) {
      trigger_error("Propriedade ({$prop}) do projeto n�o definida!", E_USER_ERROR);
    }
    switch ($prop) {
    case 'pathTileset':
      $pathOrigemTileset = $valor;
      $pathDestinoTileset = $this->pathProjeto . DIRECTORY_SEPARATOR . 'tileset.png';
      if (($pathOrigemTileset != $pathDestinoTileset) && !empty($pathOrigemTileset)) {
        if (!copy($pathOrigemTileset, $pathDestinoTileset)) {
          trigger_error('Falha ao copiar tileset para diret�rio de trabalho.', E_USER_ERROR);
        }
        $this->quebrarTileset();
      }
    default: $this->propriedades[$prop] = $valor;
    }
  }

  /**
  * Construtor (privado).
  *
  * O projeto deve ser instanciado apenas atrav�s de chamadas a criarProjeto e
  * abrirProjeto.
  * @see Projeto::criarProjeto();
  * @see Projeto::abrirProjeto();
  */
  private function __construct() { }

  /**
  * Cria um novo projeto e retorna refer�ncia a ele.
  *
  * Recebe o path de armazenamento do projeto e cria a estrutura base do arquivo
  * de defini��o do projeto. Tamb�m executa a cria��o do diret�rio do projeto,
  * onde ser�o armazenados arquivos tempor�rios do projeto.
  * Sempre � criado um novo projeto e sua refer�ncia � salva dentro da classe, 
  * o que faz com que refer�ncias externas tamb�m sejam atualizadas de acordo
  * com o novo objeto criado.
  * @param $path string Path para cria��o do projeto.
  * @return Projeto Refer�ncia para o projeto.
  */
  public static function criarProjeto($path) {
    // -- Cria��o do diret�rio de trabalho
    if (!mkdir($path)) { trigger_error('N�o foi poss�vel criar o diret�rio do projeto.', E_USER_ERROR); }
    // -- Cria��o do objeto do projeto e cria��o das layers
    self::$projeto = new Projeto();
    self::$projeto->pathProjeto = $path;
    self::$projeto->dataCriacao = date('Ymd');
    return self::$projeto;
  }

  /**
  * Abre um projeto salvo previamente.
  *
  * O projeto � aberto e sua refer�ncia � salva dentro da classe, desta forma
  * quando o projeto � aberto, sua refer�ncia � atualizada para o novo projeto
  * @param $path String Caminho do arquivo de refer�ncia do projeto.
  * @return Projeto
  */
  public static function abrirProjeto($path) {
    if (!is_file($path)) { trigger_error('N�o foi poss�vel abrir o projeto.', E_USER_ERROR); }
    self::$projeto = new Projeto();
    self::$projeto->carregarXML($path);
    return self::$projeto;
  }

  /**
  * Salva as informa��es armazenadas no objeto do projeto no arquivo de configura��o do projeto.
  * @return boolean
  */
  public function salvarProjeto() {
    $file = $this->pathProjeto . '.' . self::EXTENCAO_ARQUIVO_PROJETO;
    $domDoc = new DomDocument('1.0', 'iso-8859-1');
    $elmRoot = $domDoc->appendChild(new DomElement('projeto'));
    $elmRoot->setAttributeNode(new DOMAttr('pathProjeto', $this->pathProjeto));
    $elmRoot->setAttributeNode(new DOMAttr('dataCriacao', $this->dataCriacao));
    $elmConfig = $elmRoot->appendChild(new DomElement('configuracao'));
    foreach ($this->propriedades as $key => $valor) {
      if ((!in_array($key, array('pathProjeto', 'dataCriacao'))) && (!is_null($this->$key))) {
        $elmConfig->appendChild(new DomElement($key, $this->$key));
      }
    }
    // -- Salvando o XML de configura��o do projeto
    return (false !== file_put_contents("{$this->pathProjeto}." . self::EXTENCAO_ARQUIVO_PROJETO,
      $domDoc->saveXML()));
  }

  /**
  * Carrega um XML de configura��o salvo previamente.
  *
  * L� o XML em um objeto simplexml e carrega as propriedades do projeto e das
  * configura��es do projeto no objeto do projeto.
  * @param String $path Path onde est� salvo o xml de configura��o (com a exten��o do projeto).
  * @see Projeto::EXTENCAO_ARQUIVO_PROJETO
  */
  public function carregarXML($path) {
    if (!($spXML = simplexml_load_file($path))) {
      trigger_error('N�o foi poss�vel carregar defini��es do projeto.', E_USER_ERROR);
    }
    // -- Atributos do projeto
    foreach($spXML->attributes() as $key => $valor) { $this->$key = (string)$valor; }
    // -- Configura��es do projeto
    foreach ($spXML->configuracao[0]->children() as $key => $valor) { $this->$key = (string)$valor; }
  }

  /**
  * Quebra o tileset em tiles do tamanho configurado para o projeto.
  * 
  * Apaga os tilesets antigos e recria o diret�rio de tiles vazio. A seguir, pega
  * as configura��es do projeto e cria os tiles que ser�o utilizados para construir
  * as fases.
  */
  private function quebrarTileset() {
    $pathTiles = $this->pathProjeto . DIRECTORY_SEPARATOR . 'tiles' . DIRECTORY_SEPARATOR;
    // -- Apaga o diret�rio e seu conte�do (tiles antigos)
    if (is_dir($pathTiles)) { Filesystem::delDir($pathTiles); }
    if (!mkdir($pathTiles)) { trigger_error('N�o foi poss�vel criar o diret�rio de tiles.', E_USER_ERROR); }
    // -- Cria os tiles do projeto
    $imgTileset = imagecreatefrompng($this->pathProjeto . DIRECTORY_SEPARATOR . 'tileset.png');
    $imgTile = imagecreatetruecolor($this->larguraTile, $this->alturaTile);
    imagesavealpha($imgTile, true);
    $transparencia = imagecolorallocatealpha($imgTile, 0, 0, 0, 127);
    imagefill($imgTile, 0, 0, $transparencia);
    imagepng($imgTile, 'blank.png');
    for ($x = 0; $x < (imagesx($imgTileset) / $this->larguraTile); $x++) {
      for ($y = 0; $y < (imagesy($imgTileset) / $this->alturaTile); $y++) {
        $imgTile = imagecreatetruecolor($this->larguraTile, $this->alturaTile);
        imagealphablending($imgTile, false);
        imagesavealpha($imgTile, true);
        imagefill($imgTile, 0, 0, $transparencia);
        imagecopy($imgTile, $imgTileset, 0, 0,
          $x * $this->larguraTile,
          $y * $this->alturaTile,
          $this->larguraTile, $this->alturaTile);
        imagepng($imgTile, sprintf("{$pathTiles}%02d-%02d.png", $x, $y));
      }
    }
  }

  /**
  * Cria o conjunto de layers do projeto.
  */
  public function criarLayers() {
    $this->layers = array_pad(
      $this->layers,
      $this->quantidadeLayers,
      Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa));
  }

  /**
  * Adiciona uma nova layer em branco ao projeto.
  * @param int $pos Posi��o de inser��o da nova camada.
  */
  public function adicionarLayer($pos = null) {
    if (array_key_exists($pos, $this->layers)) {
      $tmpArray = array();
      foreach ($this->layers as $key => $item) {
        if ($key < $pos) { $tmpArray[$key] = $item; } // -- Itens anteriores ao novo
        else if ($key > $pos) { $tmpArray[$key + 1] = $item; } // -- Itens posteriores ao novo
        else { // -- Posi��o de inser��o
          $tmpArray[$key] = Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa);
        }
      }
      $this->layers = $tmpArray;
    } else { // -- Posi��o n�o existe ou � nula
      $this->layers[] = Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa);
    }
  }

  /**
  * Remove a layer na posi��o indica e reseta as chaves do array de layers.
  * @param int $pos Posi��o da camada a ser removida.
  */
  public function removerLayer($pos) {
    unset($this->layers[$pos]);
    // -- Resetando chaves do array de layers
    $this->layers = array_values($this->layers);
  }

  /**
  * Salva as layers do projeto.
  * @see Layer
  */
  public function salvarLayers() {
    foreach ($this->layers as $layer) {
      $layer->salvarLayer($this->pathProjeto);
    }
  }

  /**
  * Carrega as layers salvas no diret�rio do projeto.
  * @see Layer
  */
  public function carregarLayers() {
    $fltLayers = $this->projeto->pathProjeto . DIRECTORY_SEPARATOR . '*.' . self::EXTENCAO_ARQUIVO_LAYER;
    foreach (sort(glob($fltLayers)) as $XMLLayer) {
      $this->layers[] = Layer::carregarLayerXML($XMLLayer);
    }
  }
}
?>
