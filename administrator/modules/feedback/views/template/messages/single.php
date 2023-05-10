<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$msg=$this->msg;
echo "<div class=\"single-feedback\">";
echo "<h4 class=\"title\">".$msg->f_theme."</h4>";
echo "<div class=\"feedbackHead\"><b>".Text::_("Date")." : </b>".Date::fromSQL($msg->f_date, false)."&nbsp;";
echo "<b>".Text::_("Author")." : </b>".$msg->f_sender."<br />";
echo "<b>".Text::_("E-mail")." : </b>".$msg->f_mail."</div>";
echo "<div class=\"feedbackText\">".Text::toHtml($msg->f_text)."</div>";
echo "<div>";
?>