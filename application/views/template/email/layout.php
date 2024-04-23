<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 19.01.14
 * Time: 5:15
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $title; ?></title>
	</head>
	<body style="padding: 0; margin: 0;">
		<table cellpadding="0" cellspacing="0" width="800" border="0" style="border-collapse: collapse; width: 800px; margin: 0 auto;">
			<tr>
				<td valign="top" align="left" style="color: #fff; vertical-align: top; text-align: left; background-color: black;">
					<h1 style="padding: 20px 10px 20px 10px; color: #fff; margin: 0; font-family: Tahoma, sans-serif; line-height: 90%; font-size: 19pt; font-weight: normal;"><?php echo $title; ?></h1>
				</td>
			</tr>
			<tr>
				<td style="padding: 20px 10px 20px 10px; color: #565657; font-family: Tahoma, sans-serif; font-size: 12pt; vertical-align: top; text-align: left; height: 100px;">
					<?php echo $content ? $content : ''; ?>
				</td>
			</tr>
			<tr>
				<td style="padding: 10px 10px 20px 10px; color: #AAAAAA; font-size: 8pt; border-top: 1px solid #AAAAAA; background-color: #F0F0EE"><?php echo Force_Config::get_copyright(); ?></td>
			</tr>
		</table>
	</body>
</html>