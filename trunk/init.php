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
* Define o diretório raiz da aplicação.
*
* @const ROOT
*/
define(ROOT, dirname(__FILE__) . DIRECTORY_SEPARATOR);
/**
* Diretório de projetos padrão da aplicação.
*
* @const DIR_PROJETOS
* @see ROOT
*/
define(DIR_PROJETOS, ROOT . 'projetos' . DIRECTORY_SEPARATOR);
/**
* Diretório de controllers da aplicação.
*
* @const DIR_CONTROLLER
* @see ROOT
*/
define(DIR_CONTROLLER, ROOT . 'controller' . DIRECTORY_SEPARATOR);
/**
* Diretório de models da aplicação.
*
* @const DIR_MODEL
* @see ROOT
*/
define(DIR_MODEL, ROOT . 'model' . DIRECTORY_SEPARATOR);
/**
* Diretório de views da aplicação.
*
* @const DIR_VIEW
* @see ROOT
*/
define(DIR_VIEW, ROOT . 'view' . DIRECTORY_SEPARATOR);
/**
* Diretório de funções/classes utilitárias da aplicação.
*
* @const DIR_UTIL
* @see ROOT
*/
define(DIR_UTIL, ROOT . 'util' . DIRECTORY_SEPARATOR);

/**
* Carga de classes sobre demanda.
*/
function __autoload($class) {
  if (is_file(DIR_CONTROLLER . "{$class}.php")) {
    /**
    * Carregando controladores;
    */
    require_once(DIR_CONTROLLER . "{$class}.php");
  } else if (is_file(DIR_MODEL . "{$class}.php")) {
    /**
    * Carregando modelos de persistência;
    */
    require_once(DIR_MODEL . "{$class}.php");
  } else {
    /**
    * Carregando utilitários;
    */
    require_once(DIR_UTIL . "{$class}.php");
  }
}
?>
