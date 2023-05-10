<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$cat=$this->category;
?>
<h1 class="title"><?php echo $cat->bc_name; ?></h1>
<div><?php echo $cat->bc_comment; ?></div>
<?php foreach ($this->blogs as $blog) { ?>
<div class="blogBlock" id="blogBlock_<?php echo $blog->b_id; ?>">
	<div class="blogTitle"><a class="blogTitle" href="<?php echo Router::_("index.php?module=blog&amp;view=list&amp;psid=".$blog->b_id."&amp;alias=".$blog->b_alias); ?>"><?php echo $blog->b_name; ?></a></div>
	<div class="blogData"><?php 
		if ($blog->b_thumb) {
			$filename=BARMAZ_UF_PATH."blog".DS."blog_thumbs".DS.Files::splitAppendix($blog->b_thumb,true);
			if (Files::isImage($filename))	{
				$filelink=BARMAZ_UF."/blog_thumbs/list/".Files::splitAppendix($blog->b_thumb);
				echo "<img class=\"bThumb\" title=\"".$blog->b_title_thm."\" alt=\"".$blog->b_alt_thm."\" src=\"".$filelink."\" />";
			}
		}
		echo $blog->b_description; 
	?></div>
</div>
<?php
}
?>