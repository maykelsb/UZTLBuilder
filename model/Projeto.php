<?php
/**
* UZTLBuilder
* An app to build tiled layers for JavaME in PHP-GTK.
* Copyright (c) 2009 Maykel "Gardner" dos Santos Braz <maykelsb@yahoo.com.br>
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
* Portions created by Initial Developer are Copyright (C) 2009
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
* Classe de persistência de projetos.
*
* Faz o controle de persistência dos projetos como: criar, carregar, abrir e salvar.
* Também define informações sobre tipo de arquivo.
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
final class Projeto {

  /**
  * Referência para instância desta classe.
  * @see Projeto
  */
  private static $projeto = null;

  /**
  * Extenção utilizada pelos arquivos de definição de projetos.
  *
  * @const EXTENCAO_ARQUIVO_PROJETO
  */
  const EXTENCAO_ARQUIVO_PROJETO = 'utbp';
  /**
  * Descrição do tipo de arquivo de projeto.
  *
  * @const DESC_TIPO_ARQUIVO_PROJETO
  */
  const DESC_TIPO_ARQUIVO_PROJETO = 'Projeto UZTLBuilder';

  /**
  * XML (DomDocument) utilizado para armazenar as definições de projeto.
  */
  private $xml;

  /**
  * Diretório onde o projeto está/será armazenado.
  *
  * @var string
  */
  private $pathProjeto;
  /**
  * Define um valor de armazenamento do projeto.
  *
  * @param $path string Caminho de armazenamento do projeto.
  */
  public function setPathProjeto($path) { $this->pathProjeto = $path; }
  /**
  * Retorna o valor de armazenamento do projeto.
  *
  * @return string Caminho de armazenamento do projeto.
  */
  public function getPathProjeto() { return $this->pathProjeto; }

  /**
  * Dia, mês e ano em que o projeto foi criado.
  *
  * @var string
  */
  private $dataCriacao;
  /**
  * Define uma data de criação para o projeto.
  *
  * @param $data string Retorno de date('Ymd');
  */
  public function setDataCriacao($data) { $this->dataCriacao = $data; }
  /**
  * Retorna a data de criação.
  *
  * @return string Data de criação do projeto.
  */
  public function getDataCriacao() { return $this->dataCriacao; }

  private $larguraTile;
  private $alturaTile;
  private $larguraMapa;
  private $alturaMapa;
  private $quantidadeLayers;
  private $corDeFundo;
  private $layers = array();

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
  * Cria um novo projeto e retorna referência a ele.
  *
  * Recebe o path de armazenamento do projeto e cria a estrutura base do arquivo
  * de definição do projeto. Também executa a criação do diretório do projeto,
  * onde serão armazenados arquivos temporários do projeto.
  * @param $path string Path para criação do projeto.
  * @return Projeto Referência para o projeto.
  */
  public static function criarProjeto($path) {
    // -- Criação do diretório de trabalho
    if (!mkdir($path)) { trigger_error('Não foi possível criar o diretório do projeto.', E_USER_ERROR); }
    self::$projeto = new Projeto();
    self::$projeto->setPathProjeto($path);
    self::$projeto->setDataCriacao(date('Ymd'));
    // -- Tenta salvar o arquivo do projeto, se não conseguir faz rollback da criação do projeto.
    if (!self::$projeto->salvarProjeto()) {
      trigger_error('Não foi possível criar o projeto.', E_USER_ERROR);
      rmdir($path);
      self::$projeto = null;
    }
    return self::$projeto;
  }

  public static function abrirProjeto($path) {
    if (!is_file($path)) { trigger_error('Não foi possível abrir o projeto.', E_USER_ERROR); }
    self::$projeto = new Projeto();
    self::$projeto->carregarXML($path);
    return self::$projeto;
  }

  public function salvarProjeto() {
    $file = $this->pathProjeto . '.' . self::EXTENCAO_ARQUIVO_PROJETO;
    if (!file_exists($file)) { // -- Novo Projeto
      $this->xml = new DomDocument('1.0', 'iso-8859-1');
      $elmRoot = $this->xml->appendChild(new DomElement('projeto'));
      $elmRoot->setAttributeNode(new DOMAttr('path', $this->pathProjeto));
      $elmRoot->setAttributeNode(new DOMAttr('criacao', $this->dataCriacao));
    } else { // -- Projeto Existente
      
    }
    return (false !== file_put_contents("{$this->pathProjeto}." . self::EXTENCAO_ARQUIVO_PROJETO,
      $this->xml->saveXML()));
  }

  public function carregarXML($path) {
    $this->xml = new DomDocument();
    if (!$this->xml->loadXML(file_get_contents($path))) {
      trigger_error('Não foi possível carregar definições do projeto.', E_USER_ERROR);
    }
    // -- Carregando atributos para o objeto
    $projetoNo = $this->xml->getElementsByTagName('projeto')->item(0);
    $this->pathProjeto = $projetoNo->getAttribute('path');
    $this->dataCriacao = $projetoNo->getAttribute('criacao');
  }
}
?>
