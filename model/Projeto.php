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

    /*switch ($prop) {
    case 'pathTileset':
      //$pathOrigemTileset = $valor;
      //$pathDestinoTileset = $this->pathProjeto . DIRECTORY_SEPARATOR . 'tileset.png';
      if (($pathOrigemTileset != $pathDestinoTileset) && !empty($pathOrigemTileset)) {
        if (!copy($pathOrigemTileset, $pathDestinoTileset)) {
          trigger_error('Falha ao copiar tileset para diretório de trabalho.', E_USER_ERROR);
        }
        $this->quebrarTileset();
        $pathDestinoTileset = $valor;
      }
    default: $this->propriedades[$prop] = $valor;
    }*/



/**
* Classe de persistência de projetos.
*
* Faz o controle de persistência dos projetos como: criar, carregar, abrir e salvar.
* Também define informações sobre tipo de arquivo.
* @property string $nomeProjeto Nome do projeto, o nome do arquivo de configuração e do diretório de trabalho do projeto;
* @property int $larguraTile
* @property int $alturaTile
* @property int $larguraMapa
* @property int $alturaMapa
* @property int $quantidadeLayers
* @property string $corDeFundo
* @property string $pathTileset
* @property string $linguagemExport
* @final
*
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
final class Projeto {

  /**
  * Referência para instância desta classe.
  * @var Projeto
  * @see Projeto
  */
  private static $projeto = null;

  /**
  * Extenção utilizada pelos arquivos de definição de projetos.
  * @const EXTENCAO_ARQUIVO_PROJETO
  */
  const EXTENCAO_ARQUIVO_PROJETO = 'uzp';
  /**
  * Extenção utilizada pelos arquivos de layers.
  * @const EXTENCAO_ARQUIVO_LAYER
  */
  const EXTENCAO_ARQUIVO_LAYER = 'uzl';
  /**
  * Descrição do tipo de arquivo de projeto.
  * @const DESC_TIPO_ARQUIVO_PROJETO
  */
  const DESC_TIPO_ARQUIVO_PROJETO = 'Projeto UZTLBuilder';
  /**
  * Nome do diretório de tiles dentro da pasta de trabalho do projeto.
  * @const PATH_TILES
  */
  const PATH_TILES = 'tiles';

  /**
  * Caminho do projeto.
  * @var string
  */
  private $pathProjeto = '';

  /**
  * Data de criação do projeto no formato 'Ymd';
  * @var string
  */
  private $dataCriacao = '';

  /**
  * Armazena as layers do projeto.
  * @var array
  */
  private $layers = array();

  /**
  * Propriedades de configuração do projeto.
  * @var array
  * @see Projeto
  */
  private $propriedades = array(
    'nomeProjeto' => null,
    'larguraTile' => null,
    'alturaTile' => null,
    'larguraMapa' => null,
    'alturaMapa' => null,
    'quantidadeLayers' => null,
    'corDeFundo' => null,
    'pathTileset' => null,
    'linguagemExport' => null);

  /**
  * Retorna as propriedades da classe.
  * @param string $prop Nome da propriedade da classe.
  */
  public function __get($prop) {
    if (($prop != 'propriedades')
        && array_key_exists($prop, get_class_vars(__CLASS__))) { return $this->$prop;
    } else if (array_key_exists($prop, $this->propriedades)) { return $this->propriedades[$prop];
    } else { trigger_error("Propriedade ({$prop}) do projeto não definida!", E_USER_ERROR); }
  }

  /**
  * Define um valor para as propriedade das clase.
  * @param string $prop Nome da propriedade;
  * @param mixed $valor Novo valor para a propriedade;
  */
  public function __set($prop, $valor) {
    if (($prop != 'propriedades')
        && array_key_exists($prop, get_class_vars(__CLASS__))) { $this->$prop = $valor;
    } else if (array_key_exists($prop, $this->propriedades)) { $this->propriedades[$prop] = $valor;
    } else { trigger_error("Propriedade ({$prop}) do projeto não definida!", E_USER_ERROR); }
  }

  /**
  * Construtor (privado).
  *
  * O projeto deve ser instanciado apenas através de chamadas a criarProjeto e
  * abrirProjeto.
  * @see Projeto::criarProjeto();
  * @see Projeto::abrirProjeto();
  */
  private function __construct() { }

  /**
  * Cria um projeto e armazena o caminho do projeto, o nome e a data de criação.
  *
  * @param string $path Caminho onde o projeto será salvo;
  * @param string $nome O nome do projeto, e do diretório do projeto;
  * @return Projeto Referência para o projeto.
  */
  public static function criarProjeto($path, $nome) {
    // -- Criação do objeto do projeto e criação das layers
    self::$projeto = new Projeto();
    self::$projeto->pathProjeto = $path;
    self::$projeto->nomeProjeto = $nome;
    self::$projeto->dataCriacao = date('Ymd');
    return self::$projeto;
  }

  /**
  * Abre um projeto salvo previamente.
  *
  * O projeto é aberto e sua referência é salva dentro da classe, desta forma
  * quando o projeto é aberto, sua referência é atualizada para o novo projeto
  * @param string $path Caminho do arquivo de referência do projeto.
  * @return Projeto
  */
  public static function abrirProjeto($path) {
    if (!is_file($path)) { trigger_error('Não foi possível abrir o projeto.', E_USER_ERROR); }
    self::$projeto = new Projeto();
    self::$projeto->carregarXML($path);
    return self::$projeto;
  }

  /**
  * Salva as informações armazenadas no objeto do projeto no arquivo de configuração do projeto.
  * @return boolean
  */
  public function salvarProjeto() {
    $file = $this->pathProjeto . '.' . self::EXTENCAO_ARQUIVO_PROJETO;
    $domDoc = new DomDocument('1.0', 'iso-8859-1');
    $elmRoot = $domDoc->appendChild(new DomElement('projeto'));
    $elmRoot->setAttributeNode(new DOMAttr('pathProjeto', $this->pathProjeto));
    $elmRoot->setAttributeNode(new DOMAttr('dataCriacao', $this->dataCriacao));
    $elmConfig = $elmRoot->appendChild(new DomElement('configuracao'));
    // -- Adicionando propriedades de configuração ao XML
    foreach ($this->propriedades as $key => $valor) {
      $elmConfig->appendChild(new DomElement($key, $this->$key));
    }
    // -- Salvando o XML de configuração do projeto
    if (!file_put_contents("{$this->pathProjeto}." . self::EXTENCAO_ARQUIVO_PROJETO, $domDoc->saveXML())) {
      trigger_error('Não foi possível salvar o arquivo de configuração do projeto.', E_USER_ERROR);
    }
    // -- Criando diretório de trabalho, se já não foi criado pela janela de configuração
    if (!is_dir($this->pathProjeto)) {
      if (!mkdir($this->pathProjeto)) {
        trigger_error('Não foi possível criar o diretório de trabalho do projeto.');
      }
    }
    // -- Salvando o XML das layers
    foreach ($this->layers as $oLayer) { $oLayer->salvarLayer($this->pathProjeto); }
    return true;
  }

  /**
  * Carrega um XML de configuração salvo previamente.
  *
  * Lê o XML em um objeto simplexml e carrega as propriedades do projeto e das
  * configurações do projeto no objeto do projeto.
  * @param string $path Path onde está salvo o xml de configuração (com a extenção do projeto).
  * @see Projeto::EXTENCAO_ARQUIVO_PROJETO
  */
  public function carregarXML($path) {
    if (!($spXML = simplexml_load_file($path))) {
      trigger_error('Não foi possível carregar definições do projeto.', E_USER_ERROR);
    }
    // -- Atributos do projeto
    foreach($spXML->attributes() as $key => $valor) { $this->$key = (string)$valor; }
    // -- Configurações do projeto
    foreach ($spXML->configuracao[0]->children() as $key => $valor) { $this->$key = (string)$valor; }
  }

  /**
  * Quebra o tileset em tiles do tamanho configurado para o projeto.
  * 
  * Apaga os tilesets antigos e recria o diretório de tiles vazio. A seguir, pega
  * as configurações do projeto e cria os tiles que serão utilizados para construir
  * as fases.
  */
  private function quebrarTileset() {
    $pathTiles = $this->pathProjeto . DIRECTORY_SEPARATOR . 'tiles' . DIRECTORY_SEPARATOR;
    // -- Apaga o diretório e seu conteúdo (tiles antigos)
    if (is_dir($pathTiles)) { Filesystem::delDir($pathTiles); }
    if (!mkdir($pathTiles)) { trigger_error('Não foi possível criar o diretório de tiles.', E_USER_ERROR); }
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
      new Layer($this->larguraMapa, $this->alturaMapa));

  }

  /**
  * Adiciona uma nova layer em branco ao projeto.
  * @param int $pos Posição de inserção da nova camada.
  */
  public function adicionarLayer($pos = null) {
    if (array_key_exists($pos, $this->layers)) {
      $tmpArray = array();
      foreach ($this->layers as $key => $item) {
        if ($key < $pos) { $tmpArray[$key] = $item; } // -- Itens anteriores ao novo
        else if ($key > $pos) { $tmpArray[$key + 1] = $item; } // -- Itens posteriores ao novo
        else { // -- Posição de inserção
          $tmpArray[$key] = Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa);
        }
      }
      $this->layers = $tmpArray;
    } else { // -- Posição não existe ou é nula
      $this->layers[] = Layer::novaLayer($this->projeto->larguraMapa, $this->projeto->alturaMapa);
    }
  }

  /**
  * Remove a layer na posição indica e reseta as chaves do array de layers.
  * @param int $pos Posição da camada a ser removida.
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
  * Carrega as layers salvas no diretório do projeto.
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
