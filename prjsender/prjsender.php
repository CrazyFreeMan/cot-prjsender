<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=standalone
 * [END_COT_EXT]
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'prjsender');
cot_block($usr['auth_write']);

/* === Hook === */
foreach (cot_getextplugins('prjsender.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'update')
{
	$rcats = cot_import('cats', 'P', 'ARR');
	$rlocation = cot_import_location('rlocation');
	$rcats = (!empty($rcats)) ? implode(',', $rcats) : '';
	$rlocation = (!empty($rlocation)) ? implode(',', $rlocation) : '0,0,0';

	$db->update($db_users, array('user_prjsendercats' => $rcats, 'user_prjsenderdate' => $sys['now'], 'user_prjsenderlocation' => $rlocation), "user_id=".$usr['id']);
	
	cot_redirect(cot_url('prjsender', '', '', true));
}

$out['subtitle'] = $L['prjsender'];

$mskin = cot_tplfile(array('prjsender'), 'plug');

/* === Hook === */
foreach (cot_getextplugins('prjsender.main') as $pl)
{
	include $pl;
}
/* ===== */

$t = new XTemplate($mskin);

$allcats = cot_structure_children('projects', '', true);
$prjcats = array();
$prjcats_titles = array();
foreach($allcats as $cat)
{
	$prjcats[] = $cat;
	$prjcats_titles[] = $structure['projects'][$cat]['title'];
}

if(!empty($usr['profile']['user_prjsendercats']))
{
	$rcats = explode(',', $usr['profile']['user_prjsendercats']);
}
if(!empty($usr['profile']['user_prjsenderlocation']))
{
	list($rlocation['country'],$rlocation['region'],$rlocation['city']) = explode(',', $usr['profile']['user_prjsenderlocation']);
}

$t->assign(array(
	'PRJSENDER_FORM_ACTION' => cot_url('prjsender', 'a=update'),
	'PRJSENDER_FORM_CATS' => cot_checklistbox($rcats, 'cats', $prjcats, $prjcats_titles, '', '', false),		
	'PRJSENDER_FORM_LOCATION' => cot_select_location('rlocation', $rlocation['country'], $rlocation['region'], $rlocation['city'], true)
	
));

/* === Hook === */
foreach (cot_getextplugins('prjsender.tags') as $pl)
{
	include $pl;
}
/* ===== */

?>