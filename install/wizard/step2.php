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

?>
<form name="frm_install" id="inst_frm" action="install.php" method="post">
	<h3 class="title"><?php echo Text::_("Database server settings"); ?> : </h3>
	<?php if (Request::getInt('fail',0) == 1) echo "<h4 class=\"invalid\">".Text::_("Wrong database connection parameters")."!</h4>"; ?>
	<table class="db_server_settings">
		<tr>
			<td class="db_parameter"><?php echo Text::_('Database host'); ?>:</td>
			<td class="db_input"><input type="text" name="dbHost" value="127.0.0.1" />
			<input type="hidden" name="step" value="3" /></td>
		</tr>
		<tr>
			<td class="db_parameter"><?php echo Text::_('Database port'); ?>:</td>
			<td class="db_input"><input type="text" name="dbPort" value="3306" />
			<input type="hidden" name="step" value="3" /></td>
		</tr>
		<tr>
			<td class="db_parameter"><?php echo Text::_('Database name'); ?>:</td>
			<td class="db_input"><input type="text" name="dbName" value="" /></td>
		</tr>
		<tr>
			<td class="db_parameter"><?php echo Text::_('Database user name'); ?>:</td>
			<td class="db_input"><input type="text" name="dbUser" value="" /></td>
		</tr>
		<tr>
			<td class="db_parameter"><?php echo Text::_('Database password'); ?>:</td>
			<td class="db_input"><input type="password" name="dbPassword" value="" /></td>
		</tr>
		<tr>
			<td class="db_parameter"><?php echo Text::_('Database prefix'); ?>:</td>
			<td class="db_input"><input type="text" name="dbPrefix" value="c_" /></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="submit" value="<?php echo Text::_('Check connection'); ?>" />
	</div>
</form>