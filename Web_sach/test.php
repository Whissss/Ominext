<form action="#" method="post">
	<input type="text" name="pw1"> Pass_1<br>
	<input type="text" name="pw2"> Pass_2<br>
	<input type="submit" name="bnt_sub">
</form>
<?php 
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$p1 = $_POST['pw1'] ;
		$p2 = $_POST['pw2'] ;
		if($p1 !== $p2)
		{
			echo "lỗi";
		}
		else
		{
			echo "Oke";
		}
	}
?>