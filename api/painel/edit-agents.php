<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (!empty($_SESSION['admin_root_auth']) && $_SESSION['admin_root_auth'] === '1') {
    include __DIR__ . '/../includes/admin_header.php';
} else {
    include "includes/header.php";
}
				$data=[];

				$act = $_GET['act'];
				if($act == "edit") {
					$id = $_GET['id'];
					$users = getById("agents", $id);
				}
				?>

				<form method="post" action="save-agents.php" enctype='multipart/form-data'>
					<fieldset>
					
					
				
						
						<input name="cat" type="hidden" value="agents">
						<input name="id" type="hidden" value="<?=$id?>">
						<input name="act" type="hidden" value="<?=$act?>">
						

						
						
						
						
				
							<label>agentCode</label>
							<input class="form-control" type="text" name="agentCode" value="<?=$users['agentCode']?>" /><br>
							
							<label>senha</label>
							<input class="form-control" type="text" name="senha" value="<?=$users['senha']?>" /><br>
							
				
							
							<label>SALDO</label>
							<input class="form-control" type="text" name="saldo" value="<?=$users['saldo']?>" /><br>
							
							
										<label>agentToken</label>
							<input class="form-control" type="text" name="agentToken" value="<?=$users['agentToken']?>" /><br>
							
							
														<label>secretKey</label>
							<input class="form-control" type="text" name="secretKey" value="<?=$users['secretKey']?>" /><br>	
							<label>probganho</label>
							<input class="form-control" type="text" name="probganho" value="<?=$users['probganho']?>" /><br>
							
							<label>probbonus</label>
							<input class="form-control" type="text" name="probbonus" value="<?=$users['probbonus']?>" /><br>
							
							<label>probganhortp</label>
							<input class="form-control" type="text" name="probganhortp" value="<?=$users['probganhortp']?>" /><br>
							
							<label>probganhoinfluencer</label>
							<input class="form-control" type="text" name="probganhoinfluencer" value="<?=$users['probganhoinfluencer']?>" /><br>
							
			
							
										<label>probbonusinfluencer</label>
							<input class="form-control" type="text" name="probbonusinfluencer" value="<?=$users['probbonusinfluencer']?>" /><br>
							
										<label>probbonusinfluencer</label>
							<input class="form-control" type="text" name="probbonusinfluencer" value="<?=$users['probbonusinfluencer']?>" /><br>
							
														<label>probganhosaldo</label>
							<input class="form-control" type="text" name="probganhosaldo" value="<?=$users['probganhosaldo']?>" /><br>
							
																	<label>callbackurl</label>
							<input class="form-control" type="text" name="callbackurl" value="<?=$users['callbackurl']?>" /><br>
							
					


							
							<br>
					<input type="submit" value=" Save " class="btn btn-success">
					</form>
					<?php include "includes/footer.php";?>
				