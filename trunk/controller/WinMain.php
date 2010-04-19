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
* Classe de controle da janela principal (WinMain).
*
* @author Maykel dos Santos Braz <maykelsb@yahoo.com.br>
* @final
*/
final class WinMain extends Window {

  /**
  * Refer�ncia para o projeto e sua configura��o.
  * @var Projeto
  */
  private $projeto = null;

  private $tilesSelecionados = array(
    'principal' => null,
    'secund�rio' => null);

  /**
  * Define funcionalidades b�sicas para uma caixa de di�logo de manipula��o de arquivo.
  *
  * @param string $tituloCaixaSelecao  Com o t�tulo exibido pela caixa de di�logo;
  * @param Action $tipoCaixaSelecao Tipo da caixa de di�logo (Abrir - Gtk::FILE_CHOOSER_ACTION_OPEN, Salvar - Gtk::FILE_CHOOSER_ACTION_SAVE);
  * @return string com o caminho do arquivo selecionado.
  */
  private function dlgArquivos($tituloCaixaSelecao, $tipoCaixaSelecao) {
    $path = null;
    $dlg = new GtkFileChooserDialog($tituloCaixaSelecao, null, $tipoCaixaSelecao,
                                    array(Gtk::STOCK_OK, Gtk::RESPONSE_OK,
                                          Gtk::STOCK_CANCEL, Gtk::RESPONSE_CANCEL));
    // -- Caminho padr�o de projetos e filtro para tipo de arquivo
    $dlg->set_current_folder(DIR_PROJETOS);
    $dlg->add_filter($this->createFileFilter(Projeto::DESC_TIPO_ARQUIVO_PROJETO,
      '*.'. Projeto::EXTENCAO_ARQUIVO_PROJETO));
    if (Gtk::RESPONSE_OK == $dlg->run()) {
      $path = $dlg->get_filename();
    }
    $dlg->destroy();
    return $path;
  }

  /**
  * Cria um novo projeto.
  *
  * Um projeto � composto por um arquivo de defini��o e diret�rio de trabalho.
  * � recomendado que seja criado dentro do diret�rio 'projetos' dentro da
  * instala��o da app.
  * @see Projeto
  */
  public function criarProjeto() {
    $pathSelecionado = $this->dlgArquivos('Criar projeto', Gtk::FILE_CHOOSER_ACTION_SAVE);
    // -- Se foi selecionado algum path, cria o projeto e ativa os bot�es
    if (!is_null($pathSelecionado)) {
      $nomeProjeto = substr($pathSelecionado, strrpos($pathSelecionado, DIRECTORY_SEPARATOR) + 1);
      // -- Armazena refer�ncia do projeto criado
      $this->projeto = Projeto::criarProjeto($pathSelecionado, $nomeProjeto);
      $this->WinMain->set_title("{$this->tituloJanela} [{$nomeProjeto}]");
      if (!is_null($this->projeto)) {
        // -- Ap�s criar o projeto, habilite os bot�es de configura��o e de salvar
        $this->tbtnSalvar->set_sensitive(true);
        $this->tbtnConfigurar->set_sensitive(true);
      }
    }
  }

  /**
  * Abre um projeto criado anteriormente e redefine o t�tulo da janela com o caminho do projeto.
  */
  public function abrirProjeto() {
    $pathSelecionado = $this->dlgArquivos('Abrir projeto', Gtk::FILE_CHOOSER_ACTION_OPEN);
    if (!is_null($pathSelecionado)) {
      $this->projeto = Projeto::abrirProjeto($pathSelecionado);
      $this->atualizarFormulario();
    }
  }

  /**
  * Salva as modifica��es no projeto.
  *
  * @see Projeto::salvarProjeto()
  * @todo Colocar mensagem de projeto salvo com sucesso
  */
  public function salvarProjeto() {
    if ($this->projeto->salvarProjeto()) {
      // -- Mensagem de salvo com sucesso
    }
  }

  /**
  * Abre o formul�rio de configura��o do projeto.
  */
  public function exibirFormConfiguracao() {
    if (is_null($this->projeto)) {
      trigger_error('Nenhum projeto aberto para configura��o.', E_USER_ERROR);
    }
    $dlg = new DlgConfig($this->projeto);
    $retConfig = $dlg->run();
    if (DlgConfig::BOTAO_OK == $retConfig) {
      // -- Atualiza��o das layers do projeto
      $this->projeto->atualizarLayers();
      // -- C�pia do tileset para a pasta de trabalho do projeto
      $this->projeto->copiarTileset();
      $this->projeto->salvarProjeto();
    }
    $dlg->destroy();
  }

  /**
  * Atualiza o formul�rio principal com as configura��es do projeto.
  * 
  * <ul><li>Define o t�tulo da janela;
  * </li><li>Aplica cores de fundo;
  * </li><li>Carrega o tileset do cen�rio em constru��o;
  * </li><li>
  * </li><li>
  * </li></ul>
  */
  public function atualizarFormulario() {
    $this->atualizarTitulo();
    $this->atualizarCorDeFundo();
    $this->carregarTileset();
    $this->carregarListaLayers();
    $this->carregarAreaTrabalho();
    // -- Exibindo altera��es
    $this->WinMain->show_all();
  }

  /**
  * Atualiza o t�tulo da janela principal com o caminho do projeto.
  */
  //private function atualizarTitulo() {
    // -- Atualiza o t�tulo da janela principal
    //$this->WinMain->set_title($this->tituloJanela . ' [' . $this->projeto->pathProjeto . ']');
  //}

  /**
  * Atualiza a cor de fundo da �rea de trabalho e da �rea do tileset.
  */
  private function atualizarCorDeFundo() {
    if (!is_null($this->projeto->corDeFundo)) {
      $this->vwpAreaTrabalho->modify_bg(
        Gtk::STATE_NORMAL, GdkColor::parse($this->projeto->corDeFundo));
      $this->vwpTileset->modify_bg(
        Gtk::STATE_NORMAL, GdkColor::parse($this->projeto->corDeFundo));
    }
  }

  /**
  * Carrega o conjunto de tiles do projeto e preenche a �rea de tilesets dos projeto.
  */
  private function carregarTileset() {
    if (!is_null($this->projeto->pathTileset)) {
      if (!is_null($this->vwpTileset->get_child())) { $this->vwpTileset->remove($this->vwpTileset->get_child()); }
      // -- Construindo tabela de tiles
      list($larguraTileset, $alturaTileset) = getimagesize($this->projeto->pathTileset);
      $tblTiles = new GtkTable(($larguraTileset / $this->projeto->larguraTile),
        ($alturaTileset / $this->projeto->alturaTile));
      // -- Carregando tiles j� cortados
      $pathTile = $this->projeto->pathProjeto . DIRECTORY_SEPARATOR . Projeto::PATH_TILES . DIRECTORY_SEPARATOR;
      for ($x = 0; $x < ($larguraTileset / $this->projeto->larguraTile); $x++) {
        for ($y = 0; $y < ($alturaTileset / $this->projeto->alturaTile); $y++) {
          $frmTile = new GtkFrame();
          $frmTile->set_size_request((int)$this->projeto->larguraTile + 2,
            (int)$this->projeto->alturaTile + 2);
          $frmTile->add(GtkImage::new_from_file(sprintf("{$pathTile}%02d-%02d.png", $x, $y)));
          $tblTiles->attach($frmTile, $x, $x + 1, $y, $y + 1,
            Gtk::EXPAND + Gtk::FILL, Gtk::EXPAND + Gtk::FILL, 0, 0);
        }
      }
      $this->vwpTileset->add($tblTiles);
    }
  }

  /**
  * Cria a lista de layers de acordo com a quantidade de layers para o projeto.
  * @todo Verificar se foi definido um nome para a layers e carreg�-los.
  */
  private function carregarListaLayers() {
    if (!is_null($this->projeto->quantidadeLayers)) {
      // -- Quando edita a configura��o, precisa remover as colunas existentes
      foreach ($this->tvwLayers->get_columns() as $column) { $this->tvwLayers->remove_column($column); }
      $model = new GtkListStore(GdkPixbuf::gtype, TYPE_STRING);
      $this->tvwLayers->append_column(new GtkTreeViewColumn('Exibir', new GtkCellRendererPixBuf(), 'pixbuf', 0));
      $this->tvwLayers->append_column(new GtkTreeViewColumn('Layer', new GtkCellRendererText(), 'text', 1));
      $this->tvwLayers->set_model($model);
      #TODO Verificar se foi definido um nome para a layers e carreg�-los
      for ($x = 1; $x <= $this->projeto->quantidadeLayers; $x++) {
          $model->append(
            array(
              GdkPixbuf::new_from_file(DIR_VIEW . 'imagens' . DIRECTORY_SEPARATOR . 'layer_visivel.png'),
              "Layer {$x}"));
      }
    }
  }

  private function carregarAreaTrabalho() {
    if ((!is_null($this->projeto->larguraMapa)) && (!is_null($this->projeto->alturaMapa))) {
      // -- removendo as tabelas de camadas j� constru�das
      foreach ($this->fxdAreaTrabalho->get_children() as $child) {
        $this->fxdAreaTrabalho->remove($child);
      }
      // -- Criando as layers do projeto
      foreach ($this->projeto->layers as $layer) {
        // -- Criando a tabela de tiles da layer com tamanho suficiente para o frame que abrigar� o tile
        $tblLayer = new GtkTable((int)$this->projeto->larguraMapa * ((int)$this->projeto->larguraTile + 2),
          (int)$this->projeto->alturaMapa * ((int)$this->projeto->alturaTile + 2));
        foreach ($layer as $col => $linhaTiles) {
          foreach ($linhaTiles as $row => $tile) {
            $frmTile = new GtkFrame();
            $frmTile->set_size_request((int)$this->projeto->larguraTile + 2,
              (int)$this->projeto->alturaTile + 2);
            if (!is_null($tile)) { // -- Adicionando um tile previamente selecionado
              $frmTile->add(GtkImage::new_from_file("{$pathTile}{$tile}.png"));
              $tblLayer->attach($frmTile, $col, $col + 1, $row, $row + 1,
                Gtk::EXPAND + Gtk::FILL, Gtk::EXPAND + Gtk::FILL, 0, 0);
            } else { // -- Criando uma posi��o vazia para, posteriormente, adicionar um novo tile
              $tblLayer->attach($frmTile, $col, $col + 1, $row, $row + 1,
                Gtk::EXPAND + Gtk::FILL, Gtk::EXPAND + Gtk::FILL, 0, 0);
            }
          }
        }
        $this->fxdAreaTrabalho->put($tblLayer, 0, 0);
      }
    }
  }
}
?>
