<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<form action="<?php echo Router::_("index.php"); ?>" method="post">
	<input type="hidden" name="module" value="article" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="psid" value="<?php echo $this->articleId; ?>" />
 	<h3 class="article"><?php echo Text::_('Article parameters'); ?></h3>
	<div class="articleEdit">
		<div class="row">
			<div class="col-md-12">
				<?php echo Text::_('Identificator'); ?>:
				<?php if($this->articleId) echo $this->articleId; else echo Text::_('Unavailable while unsaved');  ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
		  		<label for="articleDate"><?php echo Text::_('Article date'); ?></label>
			</div>
			<div class="datetime_container col-md-8" >
				<?php echo HTMLControls::renderDateTimeSelector("articleDate", $this->articleDate)?>
			</div>
		</div>
		<div class="row">
			<div id="articleTitle" class="col-md-12">
				<label for="articleTitle_inp"><?php echo Text::_('Article title'); ?></label>
				<input type="text" id="articleTitle_inp" class="articleTitleEdit" required="required" name="articleTitle" value="<?php echo $this->articleTitle; ?>" size="100"/>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<label for="ArticleParent"><?php echo Text::_('Article parent'); ?></label>
				<?php echo $this->articleSelect; ?>
			</div>
		</div>
		<div class="row">
			<div  class="col-md-12">
		  		<label for="articleName"><?php echo Text::_('Alias'); ?></label>
				<input type="text" class="form-control" id="articleAlias" title="<?php echo Text::_('Article name alias'); ?>" name="articleAlias" value="<?php echo $this->articleAlias; ?>" size="20" />
			</div>
		</div>
		<div class="row">
			<div  class="col-md-12">
		  		<label for="metatitle"><?php echo Text::_('Meta title'); ?></label>
				<input type="text" class="form-control" id="metatitle" title="<?php echo Text::_('Meta title'); ?>" name="metatitle" value="<?php echo $this->metatitle; ?>" size="120" />
			</div>
		</div>
		<div class="row">
			<div  class="col-md-12">
		  		<label for="metakeywords"><?php echo Text::_('Meta keywords'); ?></label>
				<input type="text" class="form-control" id="metakeywords" title="<?php echo Text::_('Meta keywords'); ?>" name="metakeywords" value="<?php echo $this->metakeywords; ?>" size="120" />
			</div>
		</div>
		<div class="row">
			<div  class="col-md-12">
		  		<label for="metadescr"><?php echo Text::_('Meta description'); ?></label>
				<input type="text" class="form-control" id="metadescr" title="<?php echo Text::_('Meta description'); ?>" name="metadescr" value="<?php echo $this->metadescr; ?>" size="120" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="checkbox" value="1" name="articleShowTitle" id="articleShowTitle" <?php echo $this->showTitleCheck; ?> />
				<label for="articleShowTitle"><?php echo Text::_('Show article title'); ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="checkbox" value="1" name="articleShowBreadCrumb" id="articleShowBreadCrumb" <?php echo $this->showBCCheck; ?> />
				<label for="articleShowBreadCrumb"><?php echo Text::_('Show breadcrumb'); ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="checkbox" value="1" name="articleShowInfo" id="articleShowInfo" <?php echo $this->showInfoCheck; ?> />
				<label for="articleShowInfo"><?php echo Text::_('Show info'); ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="checkbox" value="1" name="articleShowInContents" id="articleShowInContents" <?php echo $this->showInContentsCheck; ?> />
				<label for="articleShowInContents"><?php echo Text::_('Show in contents'); ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="checkbox" value="1" name="articleShowChilds" id="articleShowChilds" <?php echo $this->showChildsCheck; ?> />
				<label for="articleShowChilds"><?php echo Text::_('Show childs'); ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<input type="checkbox" value="1" name="articlePublished" id="articlePublished" <?php echo $this->published; ?> />
				<label for="articlePublished"><?php echo Text::_('Published'); ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<?php echo HTMLControls::renderEditor('articleText','articleText',$this->articleText); ?>
			</div>
		</div>
		<div class="row">
			<div id="articleEditorFooter" class="buttons col-md-12">
				<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Save'); ?>" />
				<input type="button" onclick="javascript:history.back();" class="commonButton btn btn-info" value="<?php echo Text::_('Close'); ?>" />
			</div>
		</div>
	</div>
</form>