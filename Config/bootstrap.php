<?php
define('REVISION_STATUS_DRAFT', 0);			// 下書き
define('REVISION_STATUS_PUBLISHED', 1);		// 公開（公開されたもの）
define('REVISION_STATUS_PENDING', 2);		// 承認待ち
define('REVISION_STATUS_REMAND', 3);		// 差し戻し
define('REVISION_STATUS_AUTO_DRAFT', 4);	// 自動保存

Configure::write('TinyMCE.editorOptions', array(
	'mode' => 'exact',
	'theme' => 'advanced',
	'language' => "ja",	// TODO: ほかの言語にも切り替えしなければならない。
	'width' => "500px",
	'height' => "300px",
	'theme_advanced_toolbar_location' => 'top',
	'theme_advanced_toolbar_align' => 'left',
	'theme_advanced_statusbar_location' => 'bottom',
	'theme_advanced_resizing' => true,
//	'plugins' => 'table',
//	'theme_advanced_buttons1' => 'bold,italic,strikethrough,|,bullist,numlist,|,table,|,formatselect,fontsizeselect,|,visualaid,code,fullscreen,help',
//	'theme_advanced_buttons2' => 'forecolor,backcolor,removeformat,|,link,unlink,image,charmap,|,search,replace,|,undo,redo',
));

// TODO: test
if (class_exists('Purifier')) {
	Purifier::config('Auto', array(
			'Cache.SerializerPath' => APP . 'tmp' . DS . 'cache',
			'HTML.AllowedElements' => 'a, em, blockquote, p, strong, pre, code, span, div, ul, ol, li, img',
			'HTML.AllowedAttributes' => 'a.href, a.title, img.src, img.alt'
		)
	);
}
