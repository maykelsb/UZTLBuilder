<?php
final class LayerLinha extends ArrayIterator {
  public function __construct($larguraLinha) {
    for ($x = 0; $x < $larguraLinha; $x++) { $this->append(null); }
  }
  
  public function aumentarLargura($novaLargura) {
    for ($x = $this->count(); $x < $novaLargura; $x++) { $this->append(null); }
  }
  
  public function diminuirLargura($novaLargura) {
    $arr = $this->getArrayCopy();
    parent::__construct();
    foreach ($arr as $key => $value) {
      $this->append($value);
      if ($this->count() == $novaLargura) { break; }
    }
  }
}
?>
