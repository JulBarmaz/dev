<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

class adminConfig {
	public static	$adminTemplate			= 'space';
	public static	$cssOverride			= true;
	public static	$loadBootstrap			= true;
	public static	$useJQueryUIMini		= true;
	public static	$adminDefaultModule ='help';
	public static	$adminMemoryLimit	= 100;
	public static	$adminTimeLimit	= 60;
//	public static	$adminShowBreadcrumbs	= true;
	public static	$adminPagesPerPanel	= 20;
	public static	$adminRecordsPerPage	= 30;
	public static	$adminFltIncCustomFld	= 0;
//	public static	$subordinationInBasket	= true;
	public static	$adminCleanRowsPerQuery	= 10;
	public static	$adminSelectorAsTree = false;
}
?>