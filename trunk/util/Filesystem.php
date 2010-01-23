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
*/
final class Filesystem {
  /**
  *
  */
  public static final function delDir($dirPath) {
    if (false === strpos($dirPath, DIR_PROJETOS)) {
      trigger_error('Caminho inv�lido para apagar diret�rios. Se o seu projeto est� salvo '
        . 'em um lugar diferente do sugerido pelo programa, o diret�rio \'tiles\' deve '
        . 'ser apagado manualmente.', E_USER_ERROR);
    }
    if (!is_dir($dirPath)) {
      trigger_error('Caminho informado n�o � um diret�rio ou n�o existe.', E_USER_ERROR);
    }
    foreach (scandir($dirPath) as $itemInDir) {
      if (!in_array($itemInDir, array('.', '..'))) {
        if (is_dir($itemInDir)) {
          self::delDir("{$dirPath}" . DIRECTORY_SEPARATOR . "{$itemInDir}");
        } else {
          unlink("{$dirPath}" . DIRECTORY_SEPARATOR . "{$itemInDir}");
        }
      }
    }
    rmdir($dirPath);
  }

  /**
  * Recebe um path para um diret�rio ou arquivo troca as barras de separa��o pelas barras do SO.
  *
  * @param $path Caminho para normaliza��o de barras.
  * @return Caminho normalizado.
  */
  public static final function normalizarPath($path) {
    return (str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path));
  }
}
?>
