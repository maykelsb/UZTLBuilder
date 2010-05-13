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

  /**
  * Armazena o nome do tile atualmente selecionado.
  * @var string
  */
  private $sNomeTile;

  // -- Dialogs
  /**
  * Abre um projeto criado anteriormente e redefine o t�tulo da janela com o caminho do projeto.
  */
  public function abrirProjeto() {
    $pathSelecionado = $this->dlgArquivos('Abrir projeto',
                                          Gtk::FILE_CHOOSER_ACTION_OPEN,
                                          array('desc' => Projeto::DESC_TIPO_ARQUIVO_PROJETO,
                                                'ext' => '*.'. Projeto::EXTENCAO_ARQUIVO_PROJETO));
    if (!is_null($pathSelecionado)) {
      $this->projeto = Projeto::abrirProjeto($pathSelecionado);
      // -- Ap�s criar o projeto, habilite os bot�es de configura��o e de salvar
      $this->tbtnSalvar->set_sensitive(true);
      $this->tbtnConfigurar->set_sensitive(true);
      $this->tbtnExportar->set_sensitive(true);
      $this->atualizarFormulario();
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
      // -- Atualizando a interface
      $this->atualizarFormulario();
    }
    $dlg->destroy();
  }

  public function exibirFormExportar() {
    // -- Copiar conte�do para a layer em mem�ria
    $this->atualizarLayers();
    $this->projeto->dumpLayers();
    $dlg = new DlgExport(array('image' => $this->projeto->pathDump,
                               'source' => $this->projeto->pathSource));
    $dlg->run();
    $dlg->destroy();
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
    $pathSelecionado = $this->dlgArquivos('Criar projeto',
                                          Gtk::FILE_CHOOSER_ACTION_SAVE,
                                          array('desc' => Projeto::DESC_TIPO_ARQUIVO_PROJETO,
                                                'ext' => '*.'. Projeto::EXTENCAO_ARQUIVO_PROJETO));
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
        $this->tbtnExportar->set_sensitive(true);
      }
    }
  }

  /**
  * Salva as modifica��es no projeto.
  *
  * @see Projeto::salvarProjeto()
  * @todo Colocar mensagem de projeto salvo com sucesso
  */
  public function salvarProjeto() {
    $this->atualizarLayers();
    if ($this->projeto->salvarProjeto()) {
      // -- Mensagem de salvo com sucesso
    }
  }

  /**
  * Copia as refer�ncias dos tiles posicionados nas camadas para as layers no projeto.
  */
  private function atualizarLayers() {
    // -- Refer�ncia para o conte�do das layers na GUI
    $arrLayers = $this->fxdAreaTrabalho->get_children();
    foreach ($this->projeto->layers as $iKey => &$layer) {
      $arrTiles = array_reverse($arrLayers[$iKey]->get_children());
      for ($y = 0; $y < $this->projeto->alturaMapa; $y++) {
        for ($x = 0; $x < $this->projeto->larguraMapa; $x++) {
          $tile = $arrTiles[($this->projeto->larguraMapa * $y) + $x]->get_child()->get_child();
          if (is_null($tile)) { $layer[$y][$x] = null;
          } else { $layer[$y][$x] = $tile->get_name(); }
        }
      }
    }
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
    $this->WinMain->set_title("{$this->tituloJanela} [{$this->projeto->nomeProjeto}]");
    $this->atualizarCorDeFundo();
    $this->carregarTileset();
    $this->carregarListaLayers();
    $this->carregarAreaTrabalho();
    // -- Exibindo altera��es
    $this->WinMain->show_all();
  }

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
    if ('' != $this->projeto->pathTileset) {
      if (!is_null($this->vwpTileset->get_child())) { $this->vwpTileset->remove($this->vwpTileset->get_child()); }
      // -- Construindo tabela de tiles
      list($larguraTileset, $alturaTileset) = getimagesize($this->projeto->pathTileset);
      $tblTiles = new GtkTable(($larguraTileset / $this->projeto->larguraTile),
        ($alturaTileset / $this->projeto->alturaTile));
      // -- Carregando tiles j� cortados
      $pathTile = $this->projeto->pathProjeto . DIRECTORY_SEPARATOR . Projeto::PATH_TILES . DIRECTORY_SEPARATOR;
      // -- Corde fundo para aplicar nos event-boxes, que n�o s�o transparentes
      $gColor = GdkColor::parse($this->projeto->corDeFundo);
      // -- Preenchendo a grade com os tiles
      for ($y = 0; $y < ($alturaTileset / $this->projeto->alturaTile); $y++) {
        for ($x = 0; $x < ($larguraTileset / $this->projeto->larguraTile); $x++) {
          $evb = new GtkEventBox();
          $evb->set_name(sprintf("%02d-%02d", $y, $x));
          $evb->connect('button-press-event', array($this, 'selectTile'));
          $evb->modify_bg(Gtk::STATE_NORMAL, $gColor);
          $evb->add(GtkImage::new_from_file(sprintf("{$pathTile}%02d-%02d.png", $y, $x)));
          $frmTile = new GtkFrame();
          $frmTile->set_shadow_type(1);
          $frmTile->set_border_width(0);
          $frmTile->add($evb);
          $tblTiles->attach($frmTile, $x, $x + 1, $y, $y + 1,
            Gtk::EXPAND + Gtk::FILL, Gtk::EXPAND + Gtk::FILL, 0, 0);
        }
      }
      $this->vwpTileset->add($tblTiles);
    }
  }

  /**
  *
  */
  public function selectTile($widget) { $this->sNomeTile = $widget->get_name(); }

  /**
  *
  */
  public function insertTile($widget, $event) {
    switch ($event->button) {
    case 1: // -- Inserir/Sobrescrever tile
      if ('' != $this->sNomeTile) {
        $imgTile = GtkImage::new_from_file($this->projeto->pathProjeto
                                           . DIRECTORY_SEPARATOR . Projeto::PATH_TILES
                                           . DIRECTORY_SEPARATOR . $this->sNomeTile . '.png');
        $imgTile->set_name($this->sNomeTile);
        // -- Verificando se o eventbox j� n�o tem uma imagem como filho
        $evbChild = $widget->get_child();
        if (null != $evbChild) { $widget->remove($evbChild); }
        unset($evbChild);
        $widget->add($imgTile);
      }
      break;
    case 3: // -- Apagar tile
      $evbChild = $widget->get_child();
      if (null != $evbChild) { $widget->remove($evbChild); }
      unset($evbChild);
      break;
    }
    // -- Atualizando o widget para que exiba as mudan�as ocorridas em seu conte�do
    $widget->show_all();
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
    if (('' != $this->projeto->larguraMapa) && ('' != $this->projeto->alturaMapa)) {
      // -- removendo as tabelas de camadas j� constru�das
      foreach ($this->fxdAreaTrabalho->get_children() as $child) {
        $this->fxdAreaTrabalho->remove($child);
      }
      // -- Caminho dos tiles
      $pathTile = $this->projeto->pathProjeto
                . DIRECTORY_SEPARATOR
                . Projeto::PATH_TILES
                . DIRECTORY_SEPARATOR;

      // -- Criando as layers do projeto
      foreach ($this->projeto->layers as $layer) {
        // -- Criando a tabela de tiles da layer
        $altMapa = (int)$this->projeto->alturaMapa;
        $lrgMapa = (int)$this->projeto->larguraMapa;

        $tblLayer = new GtkTable($altMapa, $lrgMapa);

        // -- Corde fundo para aplicar nos event-boxes, que n�o s�o transparentes
        $gColor = GdkColor::parse($this->projeto->corDeFundo);
        foreach ($layer as $keyLinha => $linhaTiles) {
          foreach ($linhaTiles as $keyColuna => $tile) {
            $evb = new GtkEventBox();
            $evb->connect('button-press-event', array($this, 'insertTile'));
            $evb->modify_bg(Gtk::STATE_NORMAL, $gColor);

            $frmTile = new GtkFrame();
            $frmTile->set_shadow_type(1);
            $frmTile->set_size_request((int)$this->projeto->larguraTile + 4,
              (int)$this->projeto->alturaTile + 4);
            $frmTile->add($evb);

            if ('' != $tile) { // -- Adicionando um tile previamente selecionado
              $imgTile = GtkImage::new_from_file("{$pathTile}{$tile}.png");
              $imgTile->set_name($tile);
              $evb->add($imgTile);
            }
            // -- Anexando o tile � tabela da �rea de trabalho
            $tblLayer->attach($frmTile, $keyColuna, $keyColuna + 1, $keyLinha, $keyLinha + 1,
              Gtk::EXPAND + Gtk::FILL, Gtk::EXPAND + Gtk::FILL, 0, 0);
          }
        }
        $this->fxdAreaTrabalho->put($tblLayer, 0, 0);
      }
    }
  }
}
?>
