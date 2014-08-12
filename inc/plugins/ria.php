<?php

/**
 * @author Chii (https://github.com/thangnguyenngoc/mybb-random-avatars) 
 * @copyright 2014
 */

//Add hook for index
$plugins->add_hook('index_start', 'ria');

//info for index
function ria_info()
{
	return array(
		'name'			=> 'Random Index Avatars',
		'description'	=> 'Randomly show avatars in Index',
		'website'		=> 'https://github.com/thangnguyenngoc/mybb-random-avatars',
		'author'		=> 'Chii, https://github.com/thangnguyenngoc/mybb-random-avatars',
		'authorsite'	=> 'https://github.com/thangnguyenngoc/mybb-random-avatars',
		'version'		=> '1.0',
		'compatibility' => '16*,14*',
        'guid'          => '9995e106bf364b38bb2dd1849770de65'
	);
}

function ria_activate()
{
    require MYBB_ROOT.'/inc/adminfunctions_templates.php';
    global $db,$mybb;
    $query = $db->simple_select("settinggroups","COUNT(*) as rows");
	$rows = $db->fetch_field($query,"rows");
    $ria_group = array('name' => 'ria','title' => 'Random Index Avatars','description' => 'Settings for Random Index Avatars Plugin','disporder' =>$rows + 1,'isdefault' => '0',);
    $db->insert_query('settinggroups',$ria_group);
	$gid = $db->insert_id();
    $ria_setting_1 = array('name' => 'showria','title' =>'On/Off','description' =>'Display Random Index Avatars in Index?','optionscode' => 'onoff','value' => '1','disporder' => 1,'gid' => intval($gid),);
    $ria_setting_2 = array('name' => 'pofria','title' =>'Position ','description' =>'Where do you want to display Random Index Avatars?','optionscode' => 'select\nheader=Header\nfooter=Footer','value' => 'header','disporder' => 2,'gid' => intval($gid),);
    $ria_setting_3 = array('name' => 'inbria','title' =>'Custom text or banner','description' =>'You can enter text or arbitrary code for displayed in below Random Index Avatars.','optionscode' => 'textarea','value' => '','disporder' => 3,'gid' => intval($gid),);
    $ria_setting_4 = array('name' => 'limitria','title' =>'Number of Users','description' =>'How many users would be displayed?','optionscode' => 'text','value' => '10','disporder' => 4,'gid' => intval($gid),);
    $db->insert_query('settings',$ria_setting_1);
    $db->insert_query('settings',$ria_setting_2);
    $db->insert_query('settings',$ria_setting_3);
    $db->insert_query('settings',$ria_setting_4);
    rebuildsettings();
    $ria_template = array(
		"title"		=> 'ria',
		"template"	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="4" align="center"><strong>{$lang->ria}</strong></td>
</tr>
<tr>
<td class="trow1" width="100%">
{$feed1_ria}
</td>
</tr>
<tr>
<td class="trow1" width="100%">
{$feed2_ria}
</td>
</tr>
{$banner}
</table>
<div style="text-align: right; font-size: 10px;"> Index Avatars by <a href="https://github.com/thangnguyenngoc/mybb-random-avatars" target="blank">Chii</a></div><br />'),
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> "1418058000",
	);
	$db->insert_query("templates", $ria_template);
    find_replace_templatesets("index", '#{\$boardstats}#', "{\$riaf}\n{\$boardstats}");
    find_replace_templatesets("index", '#{\$header}#', "{\$header}\n{\$riah}");
}

//Deactive ria (very good because no change need)
function ria_deactivate()
{
    require MYBB_ROOT.'/inc/adminfunctions_templates.php';
    global $db;
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='ria'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('showria', 'ria')");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('pofria', 'ria')");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('inbria', 'ria')");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('limitria', 'ria')");
    $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='ria'");
    rebuildsettings();
    find_replace_templatesets("index", '#'.preg_quote('{$riaf}').'#', '',0);
    find_replace_templatesets("index", '#'.preg_quote('{$riah}').'#', '',0);
    
}

//Function of ria really easy (As easy as hot cake)
function ria()
{
    global $db, $theme, $mybb, $templates, $lang, $riaf, $riah,$ria;
    $lang->load("ria");
    if($mybb->settings['showria'] != 0)
    {
        //get today
        $todaytime = TIME_NOW - 86400;
        $query = $db->query("SELECT u.uid,u.username,u.displaygroup,u.usergroup,u.avatar FROM ".TABLE_PREFIX."users u WHERE u.avatar<>'' ORDER BY RAND() LIMIT 0,".$mybb->settings['limitria']."");
        $feed1_ria = '<ul style="list-style-type:none; margin:0px; padding:0px;">';
        while($user = $db->fetch_array($query))
        {
            $ava = '<img src="'.$user['avatar'].'" 	width="50px" height="50px" style="display:block;">';
            $profilelink = get_profile_link($user['uid']);
            $link = "<a href=\"{$mybb->settings['bburl']}/".$profilelink."\" title=\"{$user['username']}\" style=\"display:block;\">{$ava}</a>";
            $feed1_ria .= "<li style=\"display:inline-block;\">{$link}</li>";
        }
        $feed1_ria .= '</ul>';

        $query = $db->query("SELECT u.uid,u.username,u.displaygroup,u.usergroup,u.avatar FROM ".TABLE_PREFIX."users u WHERE u.avatar<>'' ORDER BY RAND() LIMIT 0,".$mybb->settings['limitria']."");
        $feed2_ria = '<ul style="list-style-type:none; margin:0px; padding:0px;">';
        while($user = $db->fetch_array($query))
        {
            $ava = '<img src="'.$user['avatar'].'"  width="50px" height="50px" style="display:block;">';
            $profilelink = get_profile_link($user['uid']);
            $link = "<a href=\"{$mybb->settings['bburl']}/".$profilelink."\" title=\"{$user['username']}\" style=\"display:block;\">{$ava}</a>";
            $feed1_ria .= "<li style=\"display:inline-block;\">{$link}</li>";
        }
        $feed2_ria .= '</ul>';
        
        //check banner
        if($mybb->settings['inbria'] != '')
        {
            $banner = '<tr><td colspan="4" class="trow1" align="center">'.$mybb->settings['inbria'].'</td></tr>';
        }
        //get template
        if($mybb->settings['pofria'] == 'header')
        {
            eval("\$riah = \"".$templates->get("ria")."\";");
            $riaf = "";
        }
        else
        {     
            eval("\$riaf = \"".$templates->get("ria")."\";");
            $riah = "";
        }
    }
}
?>