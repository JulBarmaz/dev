<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$upload_form=new aForm("upload_form", "post", "index.php",false);
$upload_form->addInput(array("ID"=>"import_file", "TYPE"=>"file", "NAME"=>"import_file", "REQUIRED"=>array("FLAG"=>1,"MESSAGE"=>Text::_("It was not specified a valid file to upload"))));
$upload_form->addInput(array("ID"=>"task", "TYPE"=>"hidden", "NAME"=>"task", "VAL"=>"processImport1C"));
$upload_form->addInput(array("ID"=>"module_1", "TYPE"=>"hidden", "NAME"=>"module", "VAL"=>"catalog"));
$upload_form->addInput(array("ID"=>"view_1", "TYPE"=>"hidden", "NAME"=>"view", "VAL"=>"exchange1c"));
$upload_form->addInput(array("CLASS"=>"commonButton btn btn-info","TYPE"=>"submit", "VAL"=>Text::_("Start"), "ID"=>"process_1", "NAME"=>"doit"));
if(isset($this->files_1) && count($this->files_1)){
	foreach($this->files_1 as $fk=>$fv) $upload_form->addInput(array("ID"=>"files_".$fk, "TYPE"=>"hidden", "NAME"=>"files_1[".$fk."]", "VAL"=>$fv));
	$script = "
				function processImport1C() {
// console.log('processing import ...');
					var current_file='';
					var current_field_id='';
					$('input[name^=\'files_1[\']').each(function(){
						current_file=$(this).val();
						current_field_id=$(this).attr('id');
						if(current_file !=''){
// console.log(current_file);
							return false;
						}
					});
					if(current_file !='' && current_field_id !=''){
						ajaxShowActivity();
						var url = siteConfig['siteUrl'] + 'index.php?option=ajax&task=processImport1C&module=catalog&view=exchange1c&filename='+current_file+'&field_id='+current_field_id;
						$.getJSON(url, {}, function(json) {
// console.dir(json);
							if(json){
								if (json.status==='success') {
									if(json.filename !='' && json.field_id !=''){
										$('#'+json.field_id).val('');
										var messages=json.messages;
										var text='';
										if(messages.length>0){
											$.each(messages, function(index, value){
												if(text !='') text=text+'<br />'+value;	
												else text=value;
											});
											$('#message_list').append('<div class=\"row\"><div class=\"col-md-12\"><p class=\"message_ok\">'+text+'</p></div></div>');
										}
										setTimeout('processImport1C()', 500);
									}
								} else if (json.status==='progress') {
									var messages=json.messages;
									var text='';
									if(messages.length>0){
										$.each(messages, function(index, value){
											if(text !='') text=text+'<br />'+value;	
											else text=value;
										});
										$('#message_list').append('<div class=\"row\"><div class=\"col-md-12\"><p class=\"message_ok\">'+text+'</p></div></div>');
									}
									setTimeout('processImport1C()', 500);
								} else {
									var messages=json.messages;
									var text='';
									if(messages.length>0){
										$.each(messages, function(index, value){
											if(text !='') text=text+'<br />'+value;	
											else text=value;
										});
										$('#message_list').append('<div class=\"row\"><div class=\"col-md-12\"><p class=\"error\">'+text+'</p></div></div>');
									}
								}
							}
							ajaxHideActivity();
						});
					} else {
						$('#message_list').append('<div class=\"row\"><div class=\"col-md-12\"><p class=\"message_ok blue\">".Text::_("Finished")." !!!</p></div></div>');
					}
				}
				$(document).ready(function(){ setTimeout('processImport1C()', 1000); });
				";
	Portal::getInstance()->addScriptDeclaration($script);
}
$download_form=new aForm("download_form", "post", "index.php",false);
$download_form->addInput(array("ID"=>"start_date", "TYPE"=>"datetimeselector", "CLASS"=>"form-control datetimeselector", "READONLY"=>true, "MAXLENGTH"=>10, "NAME"=>"start_date", "VAL"=>$this->start_date));
$download_form->addInput(array("ID"=>"end_date", "TYPE"=>"datetimeselector", "CLASS"=>"form-control datetimeselector", "READONLY"=>true, "MAXLENGTH"=>10, "NAME"=>"end_date", "VAL"=>$this->end_date));
$download_form->addInput(array("ID"=>"task", "TYPE"=>"hidden", "NAME"=>"task", "VAL"=>"processExport1C"));
$download_form->addInput(array("ID"=>"module_2", "TYPE"=>"hidden", "NAME"=>"module", "VAL"=>"catalog"));
$download_form->addInput(array("ID"=>"view_2", "TYPE"=>"hidden", "NAME"=>"view", "VAL"=>"exchange1c"));
$download_form->addInput(array("CLASS"=>"commonButton btn btn-info","TYPE"=>"submit", "VAL"=>Text::_("Start"), "ID"=>"process_2", "NAME"=>"doit"));
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager catalog-exchange1c rounded-pan rounded-pan-midi">
	<h4 class="title"><?php echo Text::_("Data exchange in 1C format");?>. <?php echo Text::_("Manual mode");?>.</h4>
	<div id="modify-wrapper">
		<ul class="nav nav-tabs" id="tabs">
			<li class="switcher<?php echo ($this->activeTab==1 ? " active" : "")?>">
				<a aria-expanded="false" href="#tab_import" data-toggle="tab"><?php echo Text::_("Import");?></a>
			</li>
			<li class="switcher<?php echo ($this->activeTab==2 ? " active" : "")?>">
				<a aria-expanded="false" href="#tab_export" data-toggle="tab"><?php echo Text::_("Export");?></a>
			</li>
		</ul>	
		<div class="tab-content float-fix">
			<div class="tab-pane<?php echo ($this->activeTab==1 ? " active" : "")?>" id="tab_import">
				<?php $upload_form->StartLayout(); ?>
				<?php if(isset($this->success_1) && count($this->success_1)) {
					echo "<div id=\"message_list\">";
					foreach($this->success_1 as $key=>$val){
						if(isset($this->messages_1[$key]) && count($this->messages_1[$key])){ 
							echo "<div class=\"row\"><div class=\"col-md-12\"><p class=\"".($val ? "message_ok" : "error")."\">".implode("<br />", $this->messages_1[$key])."</p></div></div>";
						}
					}
					echo "</div>";
				} ?>
				<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField(false,Text::_("Upload max file size").":");?></div>
				<div class="col-sm-8"><?php echo ini_get('max_file_uploads');	?>M</div></div>
				<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField(false,Text::_("Maximum size of POST data").":");?></div>
				<div class="col-sm-8"><?php echo ini_get('post_max_size');	?></div></div>
				<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField(false,Text::_("Filetype").":");?></div>
				<div class="col-sm-8"><?php echo "*.xml,*.zip";	?></div></div>
				<div class="row"><div class="col-md-12"><p class="info"><?php echo Text::_("Exchange 1c upload description");?></p></div></div>
				<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField("import_file",Text::_("Select source file").":");?></div>
				<div class="col-sm-8"><div class="fileselector"><?php $upload_form->renderInputPart("import_file");	?></div></div></div>
				<div class="buttons">
					<?php $upload_form->renderInputPart("doit"); ?>
					<a class ="relpopuptext80 btn btn-info" target="_blank" href="<?php echo Router::_("index.php?option=ajax&module=catalog&task=exchange1c_log&log=catalog"); ?>"><?php echo Text::_("Show log");?></a>
				</div>
				<?php $upload_form->endLayout(); ?>
			</div>
			<div class="tab-pane<?php echo ($this->activeTab==2 ? " active" : "")?>" id="tab_export">
				<?php $download_form->StartLayout(); ?>
				<?php if(isset($this->success_2) && count($this->success_2)) {
					foreach($this->success_2 as $key=>$val){
						if(isset($this->messages_2[$key]) && count($this->messages_2[$key])){ 
							echo "<div class=\"row\"><div class=\"col-md-12\"><p class=\"".($val ? "message_ok" : "error")."\">".implode("<br />", $this->messages_2[$key])."</p></div></div>";
						}
					}
				} ?>
				<?php if( !(isset($this->filelink) && $this->filelink) ) { ?>
					<div class="row"><div class="col-md-12"><p class="info"><?php echo Text::_("Exchange 1c download description");?></p></div></div>
					<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField("start_date", Text::_("Start date").":");?></div>
					<div class="col-sm-8"><?php $download_form->renderInputPart("start_date");	?></div></div>
					<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField("end_date", Text::_("End date").":");?></div>
					<div class="col-sm-8"><?php $download_form->renderInputPart("end_date");	?></div></div>
				<?php } ?>
				<div class="buttons">
					<?php if(isset($this->filelink) && $this->filelink) { ?>
						<a class ="btn btn-info" target="_blank" href="<?php echo $this->filelink; ?>"><?php echo HTMLControls::renderLabelField(false,Text::_("Download"));?>orders.xml</a>
					<?php } else { ?>
						<?php $download_form->renderInputPart("doit"); ?>
					<?php } ?>
					<a class ="relpopuptext80 btn btn-info" target="_blank" href="<?php echo Router::_("index.php?option=ajax&module=catalog&task=exchange1c_log&log=sale"); ?>"><?php echo Text::_("Show log");?></a>
				</div>
				<?php $download_form->endLayout(); ?>
			</div>
		</div>
	</div>
</div></div></div></div>
