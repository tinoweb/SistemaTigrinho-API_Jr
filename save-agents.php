<?php
        include("includes/connect.php");
        include("includes/data.php");

        $cat = $_POST['cat'] ?? ($_GET['cat'] ?? '');
        $cat_get = $_GET['cat'] ?? '';
        $act = $_POST['act'] ?? ($_GET['act'] ?? '');
        $act_get = $_GET['act'] ?? '';
        $id = $_POST['id'] ?? ($_GET['id'] ?? '');
        $id_get = $_GET['id'] ?? '';

		
				if($cat == "agents" || $cat_get == "agents") {
                    $agentCode = isset($_POST["agentCode"]) ? addslashes(htmlentities($_POST["agentCode"], ENT_QUOTES)) : '';
$senha = isset($_POST["senha"]) ? addslashes(htmlentities($_POST["senha"], ENT_QUOTES)) : '';
$saldo = isset($_POST["saldo"]) ? addslashes(htmlentities($_POST["saldo"], ENT_QUOTES)) : '0';
$agentToken = isset($_POST["agentToken"]) ? addslashes(htmlentities($_POST["agentToken"], ENT_QUOTES)) : '';
$secretKey = isset($_POST["secretKey"]) ? addslashes(htmlentities($_POST["secretKey"], ENT_QUOTES)) : '';
$probganho = isset($_POST["probganho"]) ? addslashes(htmlentities($_POST["probganho"], ENT_QUOTES)) : '';
$probbonus = isset($_POST["probbonus"]) ? addslashes(htmlentities($_POST["probbonus"], ENT_QUOTES)) : '';
$probganhortp = isset($_POST["probganhortp"]) ? addslashes(htmlentities($_POST["probganhortp"], ENT_QUOTES)) : '';
$probganhoinfluencer = isset($_POST["probganhoinfluencer"]) ? addslashes(htmlentities($_POST["probganhoinfluencer"], ENT_QUOTES)) : '';
$probbonusinfluencer = isset($_POST["probbonusinfluencer"]) ? addslashes(htmlentities($_POST["probbonusinfluencer"], ENT_QUOTES)) : '';
$probganhoaposta = isset($_POST["probganhoaposta"]) ? addslashes(htmlentities($_POST["probganhoaposta"], ENT_QUOTES)) : '';
$probganhosaldo = isset($_POST["probganhosaldo"]) ? addslashes(htmlentities($_POST["probganhosaldo"], ENT_QUOTES)) : '';
$callbackurl = isset($_POST["callbackurl"]) ? addslashes(htmlentities($_POST["callbackurl"], ENT_QUOTES)) : '';


               if($act == "add") {
                   // gerar valores se não fornecidos
                   if ($agentToken === '') { $agentToken = bin2hex(random_bytes(16)); }
                   if ($secretKey === '') { $secretKey = bin2hex(random_bytes(24)); }
                   // impedir duplicidade por agentCode
                   $exists = qSELECT("SELECT id FROM agents WHERE agentCode='".$agentCode."' LIMIT 1");
                   if ($exists && count($exists) > 0) {
                       header('Location: ../edit-agents.php?act=add&error=duplicate');
                       exit;
                   }
                   $maxIdRow = qSELECT("SELECT MAX(id) AS max_id FROM agents");
                   $nextId = ($maxIdRow && isset($maxIdRow[0]['max_id'])) ? ((int)$maxIdRow[0]['max_id'] + 1) : 1;
                   mysqli_query($link, "INSERT INTO `agents` ( `id`, `agentCode`, `senha`, `saldo`, `agentToken`, `secretKey`, `probganho`, `probbonus`, `probganhortp`, `probganhoinfluencer`, `probbonusinfluencer`, `probganhoaposta`, `probganhosaldo`, `callbackurl` ) VALUES ( '".$nextId."', '".$agentCode."', '".$senha."', '".$saldo."', '".$agentToken."', '".$secretKey."', '".$probganho."', '".$probbonus."', '".$probganhortp."', '".$probganhoinfluencer."', '".$probbonusinfluencer."', '".$probganhoaposta."', '".$probganhosaldo."', '".$callbackurl."' ) ");
               }elseif ($act == "edit") {

mysqli_query($link, "UPDATE `agents` SET  `agentCode` =  '".$agentCode."' , `senha` =  '".$senha."' , `saldo` =  '".$saldo."' , `agentToken` =  '".$agentToken."' , `secretKey` =  '".$secretKey."' , `probganho` =  '".$probganho."' , `probbonus` =  '".$probbonus."'  , `probganhortp` =  '".$probganhortp."'   , `probganhoinfluencer` =  '".$probganhoinfluencer."' , `probbonusinfluencer` =  '".$probbonusinfluencer."', `probganhoaposta` = '".$probganhoaposta."', `probganhosaldo` =  '".$probganhosaldo."', `callbackurl` =  '".$callbackurl."'  WHERE `id` = '".$id."' "); 
						}elseif ($act_get == "delete") {
							mysqli_query($link, "DELETE FROM `users` WHERE id = '".$id_get."' ");
						}
                    if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
                    if (!empty($_SESSION['admin_root_auth']) && $_SESSION['admin_root_auth'] === '1') {
                        header('Location: /admin/agents.php');
                    } else {
                        header('Location: agents.php');
                    }
				}
				?>