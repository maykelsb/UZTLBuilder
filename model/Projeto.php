<?php
final class Projeto {

  private static $projeto = null;

  const EXTENCAO_ARQUIVO_PROJETO = 'utbp';
  const DESC_TIPO_ARQUIVO_PROJETO = 'Projeto UZTLBuilder';

  private $xml;

  private $pathProjeto;
  public function setPathProjeto($path) { $this->pathProjeto = $path; }
  public function getPathProjeto() { return $this->pathProjeto; }

  private $dataCriacao;
  public function setDataCriacao($data) { $this->dataCriacao = $data; }
  public function getDataCriacao() { return $this->dataCriacao; }

  private $larguraTile;
  private $alturaTile;
  private $larguraMapa;
  private $alturaMapa;
  private $quantidadeLayers;
  private $corDeFundo;
  private $layers = array();

  private function __construct() { }

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
    return (false !== file_put_contents($this->xml->saveXML()));
  }
}
?>
