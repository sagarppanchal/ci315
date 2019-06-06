<?php 
	$currLang=$this->session->userdata['current_language'];
?>

<select id="lang_changer">
  <option <?php echo ($currLang == "en" ? "selected" : '' ) ?> value="en">Enlish</option>
  <option <?php echo ($currLang == "guj" ? "selected" : '' ) ?> value="guj">Gujarati</option>
  <option <?php echo ($currLang == "hin" ? "selected" : '' ) ?> value="hin">Hindi </option>
</select>

<script src="<?php echo base_url().'public/language/js/language.js' ?>"></script>