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
* Classe de persist�ncia de projetos.
*
* Faz o controle de persist�ncia dos projetos como: criar, carregar, abrir e salvar.
* Tamb�m define informa��es sobre tipo de arquivo.
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
final class Projeto {

  /**
  * Refer�ncia para inst�ncia desta classe.
  * @see Projeto
  */
  private static $projeto = null;

  /**
  * Exten��o utilizada pelos arquivos de defini��o de projetos.
  *
  * @const EXTENCAO_ARQUIVO_PROJETO
  */
  const EXTENCAO_ARQUIVO_PROJETO = 'utbp';
  /**
  * Descri��o do tipo de arquivo de projeto.
  *
  * @const DESC_TIPO_ARQUIVO_PROJETO
  */
  const DESC_TIPO_ARQUIVO_PROJETO = 'Projeto UZTLBuilder';

  /**
  * XML (DomDocument) utilizado para armazenar as defini��es de projeto.
  */
  private $xml;

  /**
  * Diret�rio onde o projeto est�/ser� armazenado.
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
  * Dia, m�s e ano em que o projeto foi criado.
  *
  * @var string
  */
  private $dataCriacao;
  /**
  * Define uma data de cria��o para o projeto.
  *
  * @param $data string Retorno de date('Ymd');
  */
  public function setDataCriacao($data) { $this->dataCriacao = $data; }
  /**
  * Retorna a data de cria��o.
  *
  * @return string Data de cria��o do projeto.
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
  * @param $path string Path para cria��o do projeto.
  * @return Projeto Refer�ncia para o projeto.
  */
  public static function criarProjeto($path) {
    // -- Cria��o do diret�rio de trabalho
    if (!mkdir($path)) { trigger_error('N�o foi poss�vel criar o diret�rio do projeto.', E_USER_ERROR); }
    self::$projeto = new Projeto();
    self::$projeto->setPathProjeto($path);
    self::$projeto->setDataCriacao(date('Ymd'));
    // -- Tenta salvar o arquivo do projeto, se n�o conseguir faz rollback da cria��o do projeto.
    if (!self::$projeto->salvarProjeto()) {
      trigger_error('N�o foi poss�vel criar o projeto.', E_USER_ERROR);
      rmdir($path);
      self::$projeto = null;
    }
    return self::$projeto;
  }

  public static function abrirProjeto() {
    return self::$project;
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
}
?>
