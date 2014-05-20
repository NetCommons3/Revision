<?php
/**
 * Revision configuration file
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author   Ryuji Masukawa <masukawa@nii.ac.jp>
 * @since 3.0.0.0
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

$config['Revision.status_id'] = array(
	'draft' => 0,				// 下書き
	'published' => 1,			// 公開（公開されたもの）
	'pending' => 2,				// 承認待ち
	'rejected' => 3,			// 差し戻し
	'auto_draft' => 4			// 自動保存
);

//return $config;