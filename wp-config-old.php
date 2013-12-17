<?php

/** Enable W3 Total Cache */

define('WP_CACHE', true); // Added by W3 Total Cache



define( 'DISALLOW_FILE_EDIT', true );

define( 'BWPS_FILECHECK', true );
/**
* As configurações básicas do WordPress.
*
* Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
* Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
* visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
* wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
*
* Esse arquivo é usado pelo script ed criação wp-config.php durante a
* instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
* como "wp-config.php" e preencher os valores.
*
* @package WordPress
*/

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'ecotempo_main');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'ecotempo_admin');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'ZpaK}GN5ni({');

/** nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
* Chaves únicas de autenticação e salts.
*
* Altere cada chave para um frase única!
* Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
*
* @since 2.6.0
*/
define('AUTH_KEY',         '3Jr/5wL[cWxFO#g><lPyYLQkACJq7zWL|a5<4@49Y8xn?o99Y-RSRb4WWIo|h@_A');
define('SECURE_AUTH_KEY',  'S_9!_]2Q:0.a bDaH?GJ;Hhj85+>bpj1)0HiK&}}se?n*3X=l-:5A-pJ~ko(?9{%');
define('LOGGED_IN_KEY',    '?w73WcI%SppFklmMY8[ZaN*vgT,V-MNHIflJkN*Q1e~;a,3HhnDRA20h/E<yTKc[');
define('NONCE_KEY',        ';QQrNT8,EH|a!+V+Cthc!}fs`Ve;(G OZB0-r=3orkzYosvP6&V,TyI5=HJ<~ J9');
define('AUTH_SALT',        '?1=PR0|f>e[*29eXereV`#63LUhWF}Z-#`-S_$gp2Y2G[pqRERKH}KyyER_r^--|');
define('SECURE_AUTH_SALT', 'wO>]7JxEDLyQ h&*U:8KlcWo4Lj>rz*q|y`m%VV/<-kbcL>r)63iV;%d_gj=24Da');
define('LOGGED_IN_SALT',   'c|-,;;Rr^LZOOu;`v1!3+*{U5#zU:O;}3qh$% au*m+/T=UNV:qfu,4iY}=kCWAX');
define('NONCE_SALT',       'h1|P#l72k8>sy?_[~t}Usp~!OJ3p%l/ZAI$HTz3pP=G;XTq!DL!=kwt>skA<&(~t');

/**#@-*/

/**
* Prefixo da tabela do banco de dados do WordPress.
*
* Você pode ter várias instalações em um único banco de dados se você der para cada um um único
* prefixo. Somente números, letras e sublinhados!
*/
$table_prefix  = 'eco_';

/**
* O idioma localizado do WordPress é o inglês por padrão.
*
* Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
* idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
* pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
* ao português do Brasil.
*/
define('WPLANG', 'pt_BR');

/**
* Para desenvolvedores: Modo debugging WordPress.
*
* altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
* é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
* em seus ambientes de desenvolvimento.
*/
// Enable WP_DEBUG mode
error_reporting(E_ALL); ini_set('display_errors', 1);
define('WP_DEBUG', true);

// Enable Debug logging to the /wp-content/debug.log file
define('WP_DEBUG_LOG', true);

// Disable display of errors and warnings 
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors',0);

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define('SCRIPT_DEBUG', true);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');

/** Trick for long posts */
ini_set('pcre.recursion_limit',20000000);
ini_set('pcre.backtrack_limit',10000000);

