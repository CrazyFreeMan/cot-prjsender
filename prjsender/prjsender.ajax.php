<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=ajax
 * [END_COT_EXT]
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'prjsender');
cot_block($usr['auth_read']);

$users_sql = $db->query("SELECT * FROM $db_users WHERE user_prjsendercats!='' AND user_prjsenderdate<".$sys['now']);
while($urr = $users_sql->fetch())
{
	$prjcats = explode(',', $urr['user_prjsendercats']);
	list($prjlocation['country'],$prjlocation['region'],$prjlocation['city']) = explode(',', $urr['user_prjsenderlocation']);
		
	if(strlen($prjlocation['country']) >= 2){
		$queryloc['country'] = "item_country = '".$prjlocation['country']."'";
	}
	if($prjlocation['region'] != 0 && !empty($prjlocation['region'])){
		$queryloc['region'] = "item_region = ".$prjlocation['region'];
	}
	if($prjlocation['city'] > 0 ){
		$queryloc['city'] = "item_city = ".$prjlocation['city'];
	}
	if (!empty($queryloc)) {
	$queryl = " AND ".implode(" AND ", $queryloc);
	}
	$prjs = $db->query("SELECT * FROM $db_projects AS p
		LEFT JOIN $db_users AS u ON u.user_id=p.item_userid
		WHERE item_state=0 AND item_cat IN ('".implode("','", $prjcats)."') AND item_userid!=".$urr['user_id']." AND item_date>".$urr['user_prjsenderdate'].$queryl)->fetchAll();
	
	if(is_array($prjs) && count($prjs) > 0)
	{
		$t = new XTemplate(cot_tplfile(array('prjsender', 'mail'), 'plug'));

		foreach($prjs as $item)
		{
			$t->assign(cot_generate_usertags($item, 'PRJ_ROW_OWNER_'));
			$t->assign(cot_generate_projecttags($item, 'PRJ_ROW_'));
			
			$t->parse("MAIN.PRJ_ROWS");
		}
		
		$t->parse('MAIN');
		
		cot_mail($urr['user_email'], $L['prjsender_mail_title'], $t->text('MAIN'), '', false, null, true);
		
		$db->update($db_users, array('user_prjsenderdate' => $sys['now']), "user_id=".$urr['user_id']);
	}
}