<html>
<head>
<?php echo $html->charset() ?>

<style style="text/css">
	td {
		border-width: 1px solid #000000;
		mso-number-format:"\@";	<?php // this will make each cell content as text ?>
	}

	td.number {
		mso-number-format: "#,##0";	<?php // this will make each cell content as number with format ?>
	}

	td.number_decimal1 {
		mso-number-format: "#,##0.0";	<?php // this will make each cell content as number with format ?>
	}

	td.number_decimal3 {
		mso-number-format: "#,##0.000";	<?php // this will make each cell content as number with format ?>
	}

	td.general {
		mso-number-format:General;	<?php // this will make each cell content as float with format ?>
	}

	td.right {
		text-align:right;
		mso-number-format:"\@";	<?php // this will make each cell content as text ?>
	}
</style>

</head>
<body>
<?php echo $content_for_layout ?>
</body>
</html>
 
