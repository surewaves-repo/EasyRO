<html>
<body>
<form method="post" action="<?php echo $action ?>" >
<?php foreach ( $keys as $key ) { ?>
<?php echo $key ?><br/>
<input type="text" name="<?php echo $key ?>" /><br/><br/>
<? } ?>
<input type="submit" value="Update" />
</form>
</body>
</html> 