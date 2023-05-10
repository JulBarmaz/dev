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

$db = $this->initDB();
$pass1 = Request::get('adminPass','');
$pass2 = Request::get('adminPass2','');
$siteDomain= Request::get('siteDomain','');
$sitePort = Request::getInt('sitePort',80);
$siteSSLPort = Request::getInt('siteSSLPort',443);
$allDemoData=Request::getInt('installAllDemo',0);
$adminEmail = Request::get('adminEmail','');
if ($pass1 != $pass2) {
    $err_txt=Text::_('Administrator passwords are not equal');
} else if (mb_strlen($pass1,DEF_CP) < 6) {
    $err_txt=Text::_('Administrator password must be at least 6-chars length');
} else if ($siteDomain== '') {
    $err_txt=Text::_('Enter site domain');
} else if ($adminEmail == '') {
    $err_txt=Text::_('Enter administrator e-mail');
} else $err_txt="";
if ($err_txt) { ?>
<h4 class="invalid">
	<?php echo $err_txt; ?>
</h4>
<form name="frm_install" id="inst_frm" action="install.php"
	method="post">
	<input type="hidden" name="step" value="3" /> 
	<input type="hidden" name="dbHost" value="<?php echo DatabaseConfig::$dbHost; ?>" /> 
	<input type="hidden" name="dbPort" value="<?php echo DatabaseConfig::$dbPort; ?>" /> 
	<input type="hidden" name="dbUser" value="<?php echo DatabaseConfig::$dbUser; ?>" /> 
	<input type="hidden" name="dbPassword" value="<?php echo DatabaseConfig::$dbPassword; ?>" />
	<input type="hidden" name="dbName" value="<?php echo DatabaseConfig::$dbName; ?>" /> 
	<input type="hidden" name="dbPrefix" value="<?php echo DatabaseConfig::$dbPrefix; ?>" />
	<div class="buttons">
		<input type="submit" value="<?php echo Text::_('Go back and fix'); ?>" />
	</div>
</form>
<?php } else {
	DatabaseConfig::$dbSecret = strtoupper(Util::generateRandomString(10, "", 1));
	$error_detected=false; $err_array=array();
	$dots=str_repeat("&nbsp;&nbsp;.", 30)."&nbsp;";
	$dots="&nbsp;";
	echo "<h3 class=\"title\">".Text::_('Finishing setup')." : </h3>";
	// Write config
	echo "<p class=\"processing\">";
	echo Text::_("Updating config files").$dots;
	if (!$this->writeConfig()) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span></p>";	else echo "<span class=\"ok\">OK</span></p>";
	// SQL files
	$sqlPath = PATH_INSTALL.'sql'.DS;
	echo "<p class=\"processing\">";
	echo Text::_("Creating core tables structure").$dots;
	// System structure
	$sqlFile = $sqlPath.'core'.DS.'table.sql';
	if (!$this->populateDatabase($db,$sqlFile,$err_array)) {
		$error_detected=true;
	}
	if ($error_detected) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";	else echo "<span class=\"ok\">OK</span>";	
	echo "</p>";
	// общий список модулей которые надо установить
	$arr_modules=array_flip(Request::get('mod_inst',array()));
	
	// общий список модулей которые надо установить с демоданными
	$arr_modules_demo=array_flip(Request::get('mod_inst_demo',array()));
	
	//Util::showArray($arr_modules);
	//Util::showArray($arr_modules_demo);
	
	//die();
	//echo "<p class=\"processing\">";
	//echo Text::_("Creating modules tables ").$dots;
	if(is_array($arr_modules)&&count($arr_modules))
	{
	    foreach($arr_modules as $k=>$v)
	    {
	        // сами таблицы
	        echo "<p class=\"processing\">";
	        // вот тут переводы названий неплохо бы читать из описателя установки что ли
	        echo Text::_("Creating modules tables ")." (".Text::_($k).") ".$dots;
	        $error_detected=false; //break;
	        $sqlFile = $sqlPath.$k.DS.'table.sql';
	        if(file_exists($sqlFile)){
	        if (!$this->populateDatabase($db,$sqlFile,$err_array)) {
	            $error_detected=true;
	        }
	        if ($error_detected) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";	else echo "<span class=\"ok\">OK</span>";
	        echo "</p>";
	        }
	        //
	        $sqlFilesModData = Files::getFiles($sqlPath.$k.DS."data".DS,array(),false);
	      //  Util::showArray($sqlFilesModData,$sqlPath.$k.DS."data".DS);
	        if (!$error_detected && (is_array($sqlFilesModData)&&count($sqlFilesModData))) {
	            echo "<p class=\"processing\">";
	            echo Text::_("Inserting data").$k." ".$dots;
	            foreach ($sqlFilesModData as $sqlFile) {
	                $error_detected=false; //break;
	                if (!$this->populateDatabase($db,$sqlPath.$k.DS."data".DS.$sqlFile["filename"],$err_array)) {
	                    $error_detected=true; //break;
	                }
	            }
	            if ($error_detected) {
	                $mc=count($err_array);
	                echo "<span class=\"invalid\">".Text::_("Failed").$err_array[$mc]." !!!</span>";}
	                else echo "<span class=\"ok\">OK</span>";
	            
	            echo "</p>";
	        }
	        //var_dump($error_detected);
	        //Util::showArray($err_array);
	        
	        // тут запрос по демо данным модуля
	        if($allDemoData||array_key_exists($k, $arr_modules_demo))
	        {
	            $sqlFilesModDataDemo = Files::getFiles($sqlPath.$k.DS."demo".DS,array(),false);
	            if (!$error_detected && (is_array($sqlFilesModDataDemo)&&count($sqlFilesModDataDemo))) {
	                echo "<p class=\"processing\">";
	                echo Text::_("Inserting demo data").$k." ".$dots;
	                foreach ($sqlFilesModDataDemo as $sqlFile) {
	                    if (!$this->populateDatabase($db,$sqlPath.$k.DS."demo".DS.$sqlFile["filename"],$err_array)) {
	                        $error_detected=true;// break;
	                    }
	                }
	                if ($error_detected) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";	else echo "<span class=\"ok\">OK</span>";
	                echo "</p>";
	            }
	            
	        }
	     }
	}
	
	// теперь вставляем данные ядра
	// System data
	$sqlFilesCoreData = Files::getFiles($sqlPath."core".DS."data".DS,array(),false);
	if (!$error_detected && (is_array($sqlFilesCoreData)&&count($sqlFilesCoreData))) {
		echo "<p class=\"processing\">";
		echo Text::_("Inserting system data").$dots;
		foreach ($sqlFilesCoreData as $sqlFile) {
		    if (!$this->populateDatabase($db,$sqlPath."core".DS."data".DS.$sqlFile["filename"],$err_array)) {
				$error_detected=true; break;
			}
		}
		if ($error_detected) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";	else echo "<span class=\"ok\">OK</span>";
		echo "</p>";
	}
	// Demo data
	if (!$error_detected && $allDemoData) {
		echo "<p class=\"processing\">".Text::_("Inserting DEMO data").$dots;
		$sqlFilesCoreDemo = Files::getFiles($sqlPath."core".DS."demo".DS,array(),false);
		if ((is_array($sqlFilesCoreDemo)&&count($sqlFilesCoreDemo))) {
		    foreach ($sqlFilesCoreDemo as $sqlFile) {
		        if (!$this->populateDatabase($db,$sqlPath."core".DS."demo".DS.$sqlFile["filename"],$err_array)) {
					$error_detected=true; break;
				}
			}
		}
		/*// тоже надо разнести по модулям
		 * if (!$error_detected){
		    
			if(!Files::copyFolder(PATH_INSTALL.'userfiles'.DS, PATH_SITE.'userfiles'.DS,true)) $error_detected=true;
		}*/
		if ($error_detected) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";	else echo "<span class=\"ok\">OK</span>";
		echo "</p>";
		
	}
	// очистка после установки  если не нужны демо данные ?? 
	if (!$error_detected && !$allDemoData) {
		echo "<p class=\"processing\">";
		echo Text::_("Cleaning data").$dots;
		$sqlFile = $sqlPath.'cleanup.sql';
		if (!$this->populateDatabase($db,$sqlFile, $err_array)) {
			$error_detected=true;
		}
		if ($error_detected) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";	else echo "<span class=\"ok\">OK</span>";
		echo "</p>";
	}
	// Write config to database
	echo "<p class=\"processing\">";
	echo Text::_("Updating config in database").$dots;
	if (!$this->writeConfigToDatabase($db)) echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>"; else echo "<span class=\"ok\">OK</span>";
	echo "</p>";

	// Setting restructure version
	Settings::setVar("restruct_version", $this->getVersionRevision());

	if (count($err_array)){
		echo "<h4 class=\"invalid\">".Text::_("Detected errors")." : </h4>";
		foreach($err_array as $curent_error){
			echo "<div class=\"found_errors\">".$curent_error."</div>";
		}
	}
	if (!$error_detected) {
		ACLObject::initialize();
		echo "<p class=\"processing\">".Text::_("Creating administrator").$dots;
		if (!User::addUser('admin', 'admin', $pass1, $adminEmail, 1,"",1,0,1)) {
			$error_detected=true;
			echo "<span class=\"invalid\">".Text::_("Failed")." !!!</span>";
		}	else {
			$sql="UPDATE #__users SET u_id=14 WHERE u_login='admin'";
			$db->setQuery($sql);
			$db->query();
			$sql="DELETE FROM #__profiles";
			$db->setQuery($sql);
			$db->query();
			Profile::addProfile(14);
			echo "<span class=\"ok\">OK</span>";
		}
		echo "</p>";
		$siteDomain= Request::get('siteDomain','#');
		$sitePort = Request::getInt('sitePort');
		$siteSSLPort = Request::getInt('siteSSLPort',443);
		if (!$error_detected) { ?>
			<h4 class="setup_ok">
				<?php echo Text::_('Barmaz erp installed successfully'); ?>
			</h4>
<?php 
			if(Request::getInt('deleteInstaller', 0)) { // Temporary disabled feature
				if(!Files::removeFolder(PATH_INSTALL, 1) || !Files::delete(PATH_FRONT."install.php")){ 
?>
				<p class="final_message">
					<?php echo Text::_('Error removing INSTALL directory'); ?> ! ! !
				</p>
				<p class="final_message">
					<?php echo Text::_('Remove INSTALL directory and file install.php before going to site'); ?> ! ! !
				</p>
<?php
				}
				ob_flush();
				header("Location: /administrator/");
			} else { 
?>
				<p class="final_message">
					<?php echo Text::_('Remove INSTALL directory and file install.php before going to site'); ?> ! ! !
				</p>
			<?php } ?>
			<p class="final_message">
				<?php echo Text::_('Check and save settings in admin mode'); ?>
				! ! !
			</p>
			<p class="final_message">
				<?php echo Text::_('Administrator login is'); ?>
				: admin
			</p>
			<div class="buttons">
				<a class="linkButton" href="<?php echo "/administrator/"; ?>"><?php echo Text::_('Admin panel'); ?></a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a class="linkButton" href="<?php echo "/"; ?>"><?php echo Text::_('Site'); ?> </a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a class="linkButton" target="_blank" href="https://BARMAZ.ru/"><?php echo Text::_('Visit BARMAZ-ERP site'); ?></a>
			</div>
<?php 
		} else { 
?>
			<h4 class="setup_failed">
				<?php echo Text::_('Barmaz erp installation failed'); ?>
			</h4>
<?php 
		}
	} else {
?>
		<h4 class="setup_failed">
			<?php echo Text::_('Barmaz erp installation failed'); ?>
		</h4>
<?php
		
	}
}
?>