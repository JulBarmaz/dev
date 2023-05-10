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
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="generator" content="Barmaz erp" />
	<meta name="description" content="" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta content="no-cache" http-equiv="cache-control" />
	<meta name="keywords" content="BARMAZ" />
	<title>Установка BARMAZ</title>
	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="install/css/basicstyle.css" />
	<link rel="stylesheet" type="text/css" href="install/css/main.css" />
	<!-- Scripts -->
	<script src="/redistribution/jquery/jquery.min.js"></script>
	<script src="/install/js/main.js"></script>
	<!--[if lte IE 7]>
	<script>
		document.location.href = '/ie.html';
	</script>
	<![endif]-->
	<!-- Script declarations -->
	<script>//<![CDATA[
	<?php $step=Request::getInt('step',0); ?>
		var tb='tb<?php echo $step; ?>';
	// ]]></script>
</head>
<body>
	<div id="install-wrapper" class="main">
		<!-- MAIN NAVIGATION -->
		<div id="install-nav-wrap" class="float-fix">
			<div id="install-nav" class="float-fix">
				<div id="install-mainnav" class="float-fix">
					<div class="install-menu float-fix">
					<h1 class="logo">
						<a target="_blank" href="http://BARMAZ.ru" title="">&nbsp;</a>
					</h1>
						<ul class="megamenu level0">
							<li class="mega"><a href="install.php" class="mega" id="tb0" title=""><span class="menu-title">Шаг 0</span></a></li>
							<li class="mega"><a class="mega" id="tb1" title=""><span class="menu-title">Шаг 1</span></a></li>
							<li class="mega"><a class="mega" id="tb2" title=""><span class="menu-title">Шаг 2</span></a></li>
							<li class="mega"><a class="mega" id="tb3" title=""><span class="menu-title">Шаг 3</span></a></li>
							<li id="last_li" class="mega"><a class="mega" id="tb4" title=""><span class="menu-title">Шаг 4</span></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div id="install-container" class="float-fix">
			<div class="float-fix">
				<div id="install-mainbody" class="float-fix">
					<div id="content" class="float-fix">
						<div class="nopad float-fix">
							<?php echo $stepHTML; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="install-footer" class="float-fix">
			<div class="install-copyright"><a href="http://BARMAZ.ru/" target="_blank">BARMAZ group</a> © 2010-<?php echo date("Y");?>. All rights reserved.</div>
		</div>
	</div>
</body>
</html>