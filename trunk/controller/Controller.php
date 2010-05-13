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
*
*
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
class Controller {

  /**
  * Referência para objeto glade da janela.
  */
  private $glade;

  public function __construct() {
    $this->glade = new GladeXML(ROOT . 'view/' . get_class($this) . '.glade');
    $this->glade->signal_autoconnect_instance($this);
  }

  /**
  * Retorna referência para os widgets da janela.
  *
  * @param $prop string Nome do widget acessado.
  * @return Widget
  */
  public function __get($prop) {
    return $this->glade->get_widget($prop);
  }

  /**
  * Cria e retorna um filtro de seleção de arquivos.
  *
  * @param $filtername string Nome do filtro para leitura humana;
  * @param $pattern string Padrão de filtragem para seleção de arquivos;
  * @return GtkFileFilter
  */
  protected function createFileFilter($filtername, $pattern) {
    $filter = new GtkFileFilter();
    $filter->set_name($filtername);
    $filter->add_pattern($pattern);
    return $filter;
  }

  /**
  * Define funcionalidades básicas para uma caixa de diálogo de manipulação de arquivo.
  *
  * @param string $tituloCaixaSelecao  Com o título exibido pela caixa de diálogo;
  * @param Action $tipoCaixaSelecao Tipo da caixa de diálogo (Abrir - Gtk::FILE_CHOOSER_ACTION_OPEN, Salvar - Gtk::FILE_CHOOSER_ACTION_SAVE);
  * @return string com o caminho do arquivo selecionado.
  */
  protected function dlgArquivos($tituloCaixaSelecao, $tipoCaixaSelecao, $arrFiltro) {
    $path = null;
    $dlg = new GtkFileChooserDialog($tituloCaixaSelecao, null, $tipoCaixaSelecao,
                                    array(Gtk::STOCK_OK, Gtk::RESPONSE_OK,
                                          Gtk::STOCK_CANCEL, Gtk::RESPONSE_CANCEL));
    // -- Caminho padrão de projetos e filtro para tipo de arquivo
    $dlg->set_current_folder(DIR_PROJETOS);
    $dlg->add_filter($this->createFileFilter($arrFiltro['desc'], $arrFiltro['ext']));
    if (Gtk::RESPONSE_OK == $dlg->run()) {
      $path = $dlg->get_filename();
    }
    $dlg->destroy();
    return $path;
  }
}
?>
