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
	<input type="hidden" name="step" value="1" />
	<?php $this->getLicense(); ?>
	<div class="buttons">
		<input type="submit" value="<?php echo Text::_('Accept license agreement'); ?>" />
	</div>
</form>