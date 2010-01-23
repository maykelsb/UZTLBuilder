<?php
final class LayerLinha extends UpzArrayIterator {

  public function __construct($larguraLinha) {
    $this->elementos = array_pad($this->elementos, $larguraLinha, null);
  }
  
  public function aumentarLargura($novaLargura) {
    $this->elementos = array_pad($this->elementos, $novaLargura, null);
  }
  
  public function diminuirLargura($novaLargura) {
    $this->elementos = array_slice($this->elementos, 0, $novaLargura);
  }
}
?>
