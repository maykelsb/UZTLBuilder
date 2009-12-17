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
* Classe de manipula��o da janela principal da aplica��o.
*
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
class WinMain extends Window {

  private $projeto;







  private $prjName;

  /**
  * Constr�ia a janela principal da aplica��o.
  *
  * Adicionalmente, define um filtro para a janela de sele��o de arquivos e 
  * define seu diret�rio inicial de trabalho.
  * @see Window.createFileFilter()
  */
  public function __construct() {
    parent::__construct();
    $filechooserbutton = $this->get_widget('filechooserbuttonLoadTileset');
    $filechooserbutton->add_filter($this->createFileFilter('PNG Tilesets', '*.png'));
    $filechooserbutton->set_current_folder(ROOT . 'projects/');
    $filechooserbutton->add_shortcut_folder(ROOT . 'projects/');
  }

  public function setBackgroundColor() {
    $colorbuttonBackGColor = $this->get_widget('colorbuttonBackGColor');
    foreach (array('viewportWorkArea', 'viewportSelection') as $wdgName) {
      $$wdgName = $this->glade->get_widget($wdgName);
      $$wdgName->modify_bg(Gtk::STATE_NORMAL, $colorbuttonBackGColor->get_color());
    }
  }

  public function loadTileSet() {
    $filechooserbutton = $this->get_widget('filechooserbuttonLoadTileset');
    $orgFileURL = $filechooserbutton->get_filename();
    $newFileURL = ROOT . 'projects/' . $this->prjName . '/tileset.png';
    if (!is_file($orgFileURL)) {
      trigger_error('Invalid file selected.', E_USER_ERROR);
    }
    // -- Copiando o tileset para o diret�rio de trabalho
    if (!copy($orgFileURL, $newFileURL)) {
      trigger_error('Failed to copy tileset to work directory.', E_USER_ERROR);
    }
    $this->breakTilesetInTiles();
    $this->loadTilesetInSelectionArea();
  }
  
  private function breakTilesetInTiles() {
    if (is_dir(ROOT . "projects/{$this->prjName}/tiles")) {
      Filesystem::delDir(ROOT . "projects/{$this->prjName}/tiles");
    }
    if (!mkdir(ROOT . "projects/{$this->prjName}/tiles/")) {
      trigger_error('Failed to create diretory "tiles".', E_USER_ERROR);
    }

    // -- Quebrando o tileset em tiles
    $wdgTileSize = $this->get_widget('spinbuttonWidthTile');
    $tileWidth = $wdgTileSize->get_value_as_int();
    $wdgTileSize = $this->get_widget('spinbuttonHeightTile');
    $tileHeight = $wdgTileSize->get_value_as_int();
    $wdgTileSize = null; unset($wdgTileSize);
    // -- Caracter�sticas da imagem
    $imgTileset = imagecreatefrompng(ROOT . 'projects/' . $this->prjName . '/tileset.png');
    $imgTile = imagecreatetruecolor($tileWidth, $tileHeight);
    imagesavealpha($imgTile, true);
    $alpha = imagecolorallocatealpha($imgTile, 0, 0, 0, 127);
    imagefill($imgTile, 0, 0, $alpha);
    // -- Gerando tiles
    for ($y = 0; $y < (imagesy($imgTileset) / $tileHeight); $y++) {
      for ($x = 0; $x < (imagesx($imgTileset) / $tileWidth); $x++) {
        $imgTile = imagecreatetruecolor($tileWidth, $tileHeight);
        imagealphablending($imgTile, false);
        imagesavealpha($imgTile, true);
        imagefill($imgTile, 0, 0, $alpha);
        imagecopy($imgTile, $imgTileset, 0, 0, $x * $tileWidth, $y * $tileHeight, $tileWidth, $tileHeight);
        imagepng($imgTile, sprintf(ROOT . "projects/{$this->prjName}/tiles/%02d-%02d.png", $x, $y));
      }
    }
  }

  public function loadTilesetInSelectionArea() {
    $wgtTilesetSelection = $this->get_widget('viewportTilesetSelection');
  }

  /**
  * Cria o diret�rio do projeto.
  *
  * @todo Criar o arquivo de defini��o do projeto!
  * @todo Colocar na chamada do bot�o novo!
  * @see ROOT
  */
  public function createProject() {
    $dlg = new GtkFileChooserDialog('Criar novo projeto...',
                                    null,
                                    Gtk::FILE_CHOOSER_ACTION_SAVE,
                                    array(
                                      Gtk::STOCK_OK, Gtk::RESPONSE_OK,
                                      Gtk::STOCK_CANCEL, Gtk::RESPONSE_CANCEL));
    // -- Caminho padr�o de projetos e filtro para tipo de arquivo
    $dlg->set_current_folder(ROOT . 'projetos/');
    $dlg->add_filter($this->createFileFilter(Projeto::DESC_TIPO_ARQUIVO_PROJETO,
                                             '*.'. Projeto::EXTENCAO_ARQUIVO_PROJETO));
    // -- Criando projeto
    if (Gtk::RESPONSE_OK == $dlg->run()) {
      $this->projeto = Projeto::criarProjeto(Filesystem::normalizarPath($dlg->get_filename()));
    }
    $dlg->destroy();
  }
}
?>