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
* Di�logo de configura��o do projeto.
*
* Permite configurar aspectos do projeto como tilesets utilizados, dimens�es do
* mapa, dimens�es dos tiles, quantidade de camadas, etc.
* @see Dialog
*/
class DlgConfig extends Dialog {

  /**
  * Retorno da caixa de di�logo quando pressionado o bot�o OK.
  * @const BOTAO_OK
  */
  const BOTAO_OK = 1;
  /**
  * Retorno da caixa de di�logo quando pressionado o bot�o CANCELAR.
  * @const BOTAO_CANCELAR
  */
  const BOTAO_CANCELAR = 0;

  /**
  * Refer�ncia para o projeto aberto.
  * @var Projeto $projeto
  */
  private $projeto;

  /**
  * Construtor.
  *
  * Inicializa a caixa de di�logo para exibi��o dos controles de configura��o. Os
  * controles s�o inicializados com valores padr�es a menos que o projeto j� esteja
  * configurado previamente, quando eles assumem os valores j� definidos.
  * @param Projeto $prj Refer�ncia para o projeto.
  */
  public function __construct(Projeto $prj) {
    parent::__construct();

    $this->projeto = $prj;
    // -- Filtros e diret�rio inicial
    $this->fcbCarrTileset->add_filter($this->createFileFilter('Arquivos PNG', '*.png'));
    // -- Inicializar comboboxes de dimens�es dos tiles
    $arTamTiles = array(8, 16, 32, 64);
    $keyLargura = 1;
    $keyAltura = 1;
    foreach ($arTamTiles as $key => $tamTile) {
      // -- Criando os itens dos comboboxes
      $this->cbLarguraTile->insert_text($key, $tamTile);
      $this->cbAlturaTile->insert_text($key, $tamTile);
      // -- Se o projeto j� foi configurado, encontra as dimens�es para sele��o
      if ($prj->larguraTile == $tamTile) { $keyLargura = $key; }
      if ($prj->alturaTile == $tamTile) { $keyAltura = $key; }
    }
    $this->cbLarguraTile->set_active($keyLargura);
    $this->cbAlturaTile->set_active($keyAltura);
    unset($arTamTiles, $keyLargura, $keyAltura);
    // -- Inicializa dimens�es do mapa

    $lrgMapa = $prj->larguraMapa;
    $altMapa = $prj->alturaMapa;

    if (!is_null($lrgMapa)) { $this->spbLarguraMapa->set_value($lrgMapa); }
      else { $this->spbLarguraMapa->set_value(10); }
    if (!is_null($altMapa)) { $this->spbAlturaMapa->set_value($altMapa); }
      else { $this->spbAlturaMapa->set_value(10); }
    // -- Quantidade de layers
    if (!is_null($prj->quantidadeLayers)) { $this->spbQtdLayers->set_value($prj->quantidadeLayers); }
    // -- Cor de fundo
    if (!is_null($prj->corDeFundo)) { $this->cbCorDeFundo->set_color(GdkColor::parse($prj->corDeFundo)); }
    // -- Exibi��o do tileset
    if (!is_null($prj->pathTileset)) { $this->imgTileset->set_from_file($prj->pathTileset); }
    // -- Linguagem export
    $arLinguagemExport = array('JME');
    $lingExportAtivo = 0;
    foreach ($arLinguagemExport as $key => $lingExport) {
      $this->cbLinguagemExport->insert_text($key, $lingExport);
      if ($prj->linguagemExport == $lingExport) { $this->cbLinguagemExport->set_active($key); }
      else { $this->cbLinguagemExport->set_active($lingExportAtivo); }
    }
    unset($arLinguagemExport, $lingExportAtivo);
    $this->atualizaCorDeFundo();
  }

  /**
  * Sobrecarga da exibi��o normal da caixa de di�logo.
  *
  * Caso o usu�rio confirme os dados da caixa de di�logo, os valores s�o validados
  * e caso esteja tudo ok, o projeto � atualizado/
  * @return int ID do bot�o acionado pelo usu�rio;
  * @see DlgConfig::BOTAO_OK
  * @see DlgConfig::BOTAO_CANCELAR
  */
  public function run() {
    $r = parent::run();
    if (DlgConfig::BOTAO_OK == $r) {
      // -- Atualiza��o dos dados do projeto
      $this->projeto->larguraTile = $this->cbLarguraTile->get_active_text();
      $this->projeto->alturaTile = $this->cbAlturaTile->get_active_text();
      $this->projeto->larguraMapa = $this->spbLarguraMapa->get_value_as_int();
      $this->projeto->alturaMapa = $this->spbAlturaMapa->get_value_as_int();
      $this->projeto->quantidadeLayers = $this->spbQtdLayers->get_value_as_int();
// -- Downgrade de c�digo para compatibilidade com o gerador de execut�rio
      $this->projeto->corDeFundo
        = GtkColorSelection::palette_to_string($this->cbCorDeFundo->get_color(), 1);
//      $this->projeto->corDeFundo = GtkColorSelection::palette_to_string(
//                                     array($this->cbCorDeFundo->get_color()));
// -- Fim do downgrade
      $this->projeto->linguagemExport = $this->cbLinguagemExport->get_active_text();
      $this->projeto->pathTileset = $this->fcbCarrTileset->get_filename();
    }
    return $r;
  }

  /**
  * Carrega o arquivo de imagem escolhido na �rea de preview do tileset.
  */
  public function previewTileset() {
    $this->imgTileset->set_from_file(
      $this->fcbCarrTileset->get_filename());
  }

  /**
  * Atualiza a cor de fundo do preview do tileset de acordo com a cor selecionada.
  */
  public function atualizaCorDeFundo() {
    $this->viewportTileset->modify_bg(
      Gtk::STATE_NORMAL, $this->cbCorDeFundo->get_color());
  }

  /**
  * Verifica se as dimens�es da imagem s�o compat�veis com as do tile.
  * @return bool Verdadeiro(se est� tudo certo) ou falso (se as dimens�es est�o corretas).
  * @todo N�O UTILIZADA AINDA!!!
  */
  public function validarDimensoes() {
    // -- Recuperando dimens�es de tiles e da imagem carregada
    $req = $this->imgTileset->size_request();
    $larImagem = $req->width;
    $altImagem = $req->height;
    $larTile = (int)$this->cbLarguraTile->get_active_text();
    $altTile = (int)$this->cbLarguraTile->get_active_text();
    // -- Validando largura do tile vs largura da imagem
    if (($larImagem % $larTile) != 0) {
      trigger_error("A largura da imagem({$larImagem} px) devem ser m�ltipla da largura do tile({$larTile} px).",
        E_USER_WARNING);
      return false;
    }
    // -- Validando altura do tile vs altura da imagem
    if (($altImagem % $altTile) != 0) {
      trigger_error("A altura da imagem({$altImagem} px) devem ser m�ltipla da altura do tile({$altTile} px).",
        E_USER_WARNING);
      return false;
    }
    return true;
  }
}
?>
