<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、インストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さずにこのファイルを "wp-config.php" という名前でコピーして
 * 直接編集して値を入力してもかまいません。
 *
 * このファイルは、以下の設定を含みます。
 *
 * * MySQL 設定
 * * 秘密鍵
 * * データベーステーブル接頭辞
 * * ABSPATH
 *
 * @link https://ja.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// 注意:
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.osdn.jp/%E7%94%A8%E8%AA%9E%E9%9B%86#.E3.83.86.E3.82.AD.E3.82.B9.E3.83.88.E3.82.A8.E3.83.87.E3.82.A3.E3.82.BF 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define( 'DB_NAME', 'wp_tvdb' );

/** MySQL データベースのユーザー名 */
define( 'DB_USER', 'root' );

/** MySQL データベースのパスワード */
define( 'DB_PASSWORD', 'root' );

/** MySQL のホスト名 */
define( 'DB_HOST', 'localhost' );

/** データベースのテーブルを作成する際のデータベースの文字セット */
define( 'DB_CHARSET', 'utf8mb4' );

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define( 'DB_COLLATE', '' );

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ki(-5G*m*4<i#xeXCo,QpbSWcqB}qbHdr(}D--I%MKP=s(,%o-t:aar$bH*yib43' );
define( 'SECURE_AUTH_KEY',  'Q/Q*p7z0!Co2TAcPxD J7HNdQ/s)O9IdtW@w0m_I$m*?V]23+Bl-&X6Jc)#??ULP' );
define( 'LOGGED_IN_KEY',    'fk+#peHuF*?LHBP  S~;2wOx?{oy(58R2Cy}=;Zn$VzozNh,b_ XAv0% IYgVwJi' );
define( 'NONCE_KEY',        'Y|94PS:O.~lTH33w2Vw3+j@zjAx$ua8D.gq1jaEC|z_YTZxS~%Kh<K])B/n_^9u1' );
define( 'AUTH_SALT',        '{tg#H$1R`}|~a&j1tv T?<H6)H0c:$],97}BtKJ(9~HgT3QilLmrn<F;/5!o}d0Q' );
define( 'SECURE_AUTH_SALT', '(+<;^.Y:s^[9UsoG=yVC|i%GhfkY|$ejf3H{MtE]vY KC1oX+*@mre5(7UaH{A9x' );
define( 'LOGGED_IN_SALT',   'Aa!7efleFSmDF^}}|LJaZHF4t}fDrqPR&8:Fpb}g3Lm*gE5l<W(DoJz@;Is*< @V' );
define( 'NONCE_SALT',       'y{G]XJZV6H:i<q^%T5vzSE)28hQgP?5~|PIxA*gb]UjF9Q2-|p^W<=q-1&G#YIf5' );

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix = 'wp_';

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 *
 * その他のデバッグに利用できる定数についてはドキュメンテーションをご覧ください。
 *
 * @link https://ja.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* カスタム値は、この行と「編集が必要なのはここまでです」の行の間に追加してください。 */



/* 編集が必要なのはここまでです ! WordPress でのパブリッシングをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
