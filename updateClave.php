<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/conexion.php");
//include('verificarDatos/config.php');
$id 		    = $_REQUEST['id'];
$tokenUser 		= $_REQUEST['tokenUser'];
$password       = $_REQUEST['nueva_pass'];
$momento_actual = date('Y-m-d H:i:s');

//$updateClave    = ("UPDATE login SET password='$password' WHERE id='".$id."' AND tokenUser='".$tokenUser."' ");
//$queryResult    = mysqli_query($con,$updateClave); 
if ($id!=''&&$tokenUser!=''&&$password!='')
{
    $requete = "SELECT * FROM `APP_token_enlace` WHERE `nif`=".$id." AND `token_caducidad`>='".date("Y-m-d H:i:s", strtotime('+2 hours'))."'";
    $result = mysqli_query($db,$requete);
    if (($result) && (mysqli_num_rows($result)>0))
    {
        $requete2 = "UPDATE `APP_login` SET `pass`='".hash('sha256',$password)."' WHERE `nif`=".$id;		
        print $requete2;			
        if (mysqli_query($db,$requete2))
        {
            print '<meta http-equiv="refresh" content="0;url=index.php?enlace=1"/>';
        }
        else
        {
            print '<meta http-equiv="refresh" content="0;url=index.php?enlace=2"/>';
        }
    }
    else
    {
        print '<meta http-equiv="refresh" content="0;url=index.php?enlace=2"/>';
    }
}
else
{
    print '<meta http-equiv="refresh" content="0;url=index.php?enlace=2"/>';
}


?>

<!--<meta http-equiv="refresh" content="0;url=index.php?email=1"/>-->