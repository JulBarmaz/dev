<?php
//  BARMAZ erp system
//  Copyright (c) BARMAZ Group
//  Web: https://BARMAZ.ru/
//  Commercial license https://BARMAZ.ru/article/litsenzionnoe-soglashenie.html
//  THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//  Revision: 135 (2023-05-10 14:11:23)
// 

defined('_BARMAZ_VALID') or die("Access denied");

$settings_ok=true; $messages=array();
$messages[0]=array("text"=>Text::_('Configuration directory is writeable'),"check"=>"OK");
$messages[1]=array("text"=>Text::_("PHP settings:")." zend.ze1_compatibility_mode=off","check"=>"OK");
$messages[2]=array("text"=>Text::_("PHP settings:")." register_globals=off","check"=>"OK");
$messages[3]=array("text"=>Text::_("PHP version:")." >=5.3.0","check"=>"OK");

if (!is_writable(PATH_CONFIG)) { $messages[0]["check"]="Failed"; $settings_ok = false; }
if (intval(ini_get('zend.ze1_compatibility_mode')) == 1) { $messages[1]["check"]="Failed"; $settings_ok = false; }
if (intval(ini_get('register_globals')) == 1)  { $messages[2]["check"]="Failed"; $settings_ok = false; }
if (version_compare(phpversion(), '5.3.0', '<') == true)  { $messages[3]["check"]="Failed"; $settings_ok = false; }

if($settings_ok) { $button_text=Text::_('Next'); 	$install_step=2; } 
else { 	$button_text=Text::_('Repeat'); 	$install_step=1; }
?>
<h3 class="title"><?php echo Text::_("Checking server settings"); ?> : </h3>
<form name="frm_install" id="inst_frm" action="install.php" method="post">
	<input type="hidden" name="step" value="<?php echo $install_step; ?>" />
	<table class="check_settings">
	<?php 
		foreach($messages as $mess){
			if ($mess["check"]=="OK"){ $td_class="check_ok"; } else { $td_class="check_invalid"; }
			echo "<tr><td class=\"check_text\">";
			echo $mess["text"];
			echo "</td><td class=\"".$td_class."\">";
			echo Text::_($mess["check"]);
			echo "</td></tr>";
		}
	?>
	</table>
	<div class="buttons"><input type="submit" value="<?php echo $button_text;?>" /></div>
</form>
