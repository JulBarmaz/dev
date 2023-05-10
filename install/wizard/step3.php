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

$server_uri = $_SERVER['SERVER_NAME'];
$server_port=($_SERVER['SERVER_PORT']==443 ? 80 : $_SERVER['SERVER_PORT']);
$this->initDB();
$_templates=Files::getFolders(PATH_TEMPLATES.DS,array(".git",".svn",".",".."));
$_admintemplates=Files::getFolders(PATH_ADMIN_TEMPLATES.DS,array(".git",".svn",".",".."));
$sqlPath = PATH_INSTALL.'sql'.DS;
?>
<h3 class="title"><?php echo Text::_('Site custom settings'); ?> : </h3>
<form name="frm_install" id="inst_frm" action="install.php" method="post">
	<input type="hidden" name="dbHost" value="<?php echo DatabaseConfig::$dbHost; ?>" />
	<input type="hidden" name="dbPort" value="<?php echo DatabaseConfig::$dbPort; ?>" />
	<input type="hidden" name="dbUser" value="<?php echo DatabaseConfig::$dbUser; ?>" />
	<input type="hidden" name="dbPassword" value="<?php echo DatabaseConfig::$dbPassword; ?>" />
	<input type="hidden" name="dbName" value="<?php echo DatabaseConfig::$dbName; ?>" />
	<input type="hidden" name="dbPrefix" value="<?php echo DatabaseConfig::$dbPrefix; ?>" />
	<input type="hidden" name="step" value="4" />
	<table class="site_settings">
		<tr>
			<td class="site_parameter"><?php echo Text::_('Site domain'); ?>:</td>
			<td class="site_input"><input type="text" name="siteDomain" value="<?php echo $server_uri; ?>" /></td>
		</tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Site port'); ?>:</td>
			<td class="site_input"><input type="text" name="sitePort" value="<?php echo $server_port; ?>" /></td>
		</tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Site SSL port'); ?>:</td>
			<td class="site_input"><input type="text" name="siteSSLPort" value="443" /></td>
		</tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Site title'); ?>:</td>
			<td class="site_input"><input type="text" name="siteTitle" value="BARMAZ DEMO site" /></td>
		</tr>
</table>
<table class="site_settings">
		<tr><td class='str_title'><?php echo Text::_('Distribution kit'); ?>:</td></tr>
		</tr><td class="site_input"><?php $this->getDistributionKit(true); ?></td></tr>		
</table>
<table class="site_settings">		
		
		<tr>
			<td class="site_parameter"><?php echo Text::_('Admin template'); ?>:</td>
			<td class="site_input">
			<?php echo HTMLControls::renderSelect("adminTemplate","adminTemplate", 0, 'filename', $_admintemplates, 'space', 0); ?>
			</td>
		</tr>

		<tr>
			<td class="site_parameter"><?php echo Text::_('Site template'); ?>:</td>
			<td class="site_input">
			<?php echo HTMLControls::renderSelect("siteTemplate","siteTemplate", 0, 'filename', $_templates, 'html5', 0); ?>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
<!--		<tr>
			<td class="site_parameter"><?php echo Text::_('Install demo data'); ?>:</td>
			<td class="site_checkbox"><input type="checkbox" checked="checked" value="1" name="installDemo" /></td>
		</tr>
-->
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Administrator e-mail'); ?>:</td>
			<td class="site_input"><input type="text" name="adminEmail" value="email@email.ru" /></td>
		</tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Administrator password')." (".Text::_('minimum length 6 characters').")"; ?>:</td>
			<td class="site_input"><input type="text" name="adminPass" value="" /></td>
		</tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Repeat administrator password'); ?>:</td>
			<td class="site_input"><input type="text" name="adminPass2" value="" /></td>
		</tr>
		<!-- 
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td class="site_parameter"><?php echo Text::_('Delete installer after installation complete'); ?>:</td>
			<td class="site_checkbox"><input type="checkbox" checked="checked" value="1" name="deleteInstaller" /></td>
		</tr>
		-->
		<tr>
			<td class="site_parameter" colspan="2"><p class="final_message"><?php echo Text::_('Install process may take a several minutes'); ?></p></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="submit" value="<?php echo Text::_('Install'); ?>" />
	</div>
</form>
