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
* Classe base para manipulação de janelas criadas com glade.
*
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
*/
abstract class Window {

  /**
  * Referência para objeto glade da janela.
  */
  private $glade;
  /**
  * Referência direta para a janela desenhada no arquivo glade.
  */
  protected $window;
  /**
  * Título original da janela.
  */
  protected $tituloJanela;

  /**
  * Carrega o arquivo glade da janela, conecta sinais e a exibe.
  */
  public function __construct() {
    $this->glade = new GladeXML('view/' . get_class($this) . '.glade');
    $this->glade->signal_autoconnect_instance($this);
    $this->window = $this->glade->get_widget(get_class($this));
    $this->tituloJanela = $this->window->get_title();
    $this->window->show_all();
  }

  /**
  * Fecha a janela principal finalizando a aplicação.
  */
  public function closeWindow() {
    Gtk::main_quit();
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
  * Exibe a caixa de diálogo about, se existir.
  */ 
  public function showAbout() {
    $aboutdialog = new DlgAbout;
    if (!is_null($aboutdialog)) {
      $aboutdialog->run();
      $aboutdialog->destroy();
    }
  }

  /**
  * Método de atalho.
  */
  protected function get_widget($wdgName) {
    return $this->glade->get_widget($wdgName);
  }
}
?>
