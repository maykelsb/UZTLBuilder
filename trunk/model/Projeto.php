<?php
class Projeto {

  private $mainWindow;

  public function __construct($win) {
    $this->mainWindow = $win;
  }

  public function carregarProjeto() {
    return $this;
  }

  public function criarProjeto() {
    return $this;
  }

  private function __construct($nomeProjeto) {
    if (is_file($nomeProjeto)) {
      // -- Carregar projeto
    } else {
      // -- Criar projeto
    }
  }

  private function 
  
  
}
?>
