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
* Dialog de exporta��o das fases constru�das.
*
* @see Dialog
*/
class DlgExport extends Dialog {
  private $mockupPath;

  public function __construct($arrDumps) {
    parent::__construct();
    // -- Exibi��o do mockup da fase criada.
    $this->mockupPath = $arrDumps['image'];
    $this->sourcePath = $arrDumps['source'];
    $this->imgMockup->set_from_file($arrDumps['image']);
  }

  /**
  * Salva o arquivo png com o conte�do da fase constru�da.
  */
  public function indicaDestinoImagem() {
    $pathDestino = $this->dlgArquivos('Salvar imagem',
                                      Gtk::FILE_CHOOSER_ACTION_SAVE,
                                      array('desc' => 'Arquivo PNG',
                                            'ext' => '*.png'));
    if (!strpos($pathDestino, '.png')) { $pathDestino .= '.png'; }

    if (!is_null($pathDestino)) {
      if (!copy($this->mockupPath, $pathDestino)) {
        trigger_error('N�o foi poss�vel criar o arquivo destino.');
      }
    }
  }

  /**
  * Salva um arquivo txt com o conte�do da fase constru�da no formato utilizado pelo javame.
  *
  * Formato JME: tiles vazios recebem o valor '0', e a contagem dos tiles no
  * tileset iniciam em '1'. � poss�vel criar anima��es de tiles no javame, no
  * entanto, este recurso ainda n�o � suportado pela aplica��o.
  */
  public function indicaDestinoSource() {
    $pathDestino = $this->dlgArquivos('Salvar source',
                                      Gtk::FILE_CHOOSER_ACTION_SAVE,
                                      array('desc' => 'Source TXT',
                                            'ext' => '*.txt'));
    if (!strpos($pathDestino, '.txt')) { $pathDestino .= '.txt'; }
    if (!is_null($pathDestino)) {
      if (!copy($this->sourcePath, $pathDestino)) {
        trigger_error('N�o foi poss�vel criar o arquivo destino.');
      }
    }
  }
}
?>
