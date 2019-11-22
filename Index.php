<?php
$page = $_SERVER['PHP_SELF'];
$sec = "10";

?>
<html>
    <head>
    </head>
    <body>
        <?php
        include('conn.php');
include('func.php');
try 
{
    $resultCCYsite=mysqli_query($conn,"SELECT CCY,SiteURL FROM tbl_ccysite");
    if(mysqli_num_rows($resultCCYsite)==0)   
    {
        echo "Site URL Empty";
        SendMail("Site Not Found","2.2 Site URL Empty");
    }    
    while($dataCCYsite=mysqli_fetch_array($resultCCYsite))
    {
        $json_string = $dataCCYsite['SiteURL'];
        $jsondata = file_get_contents($json_string);
        $obj = json_decode($jsondata,true);
       
        $result=mysqli_query($conn,"INSERT INTO tbl_PriceList(HIGH,LOW,CURRENT,CreatedDate,CCY) 
        			  VALUES('{$obj['ticker']['high']}' , '{$obj['ticker']['low']}' , '{$obj['ticker']['last']}' , now(),'{$dataCCYsite['CCY']}')");

        if($result)
        {
            $resultSetPrice=mysqli_query($conn,"SELECT PriceMin,PriceMax,PriceRange FROM tbl_SetPrice WHERE CCY='{$dataCCYsite['CCY']}'");
            if(mysqli_num_rows($resultSetPrice)==0)   
            {
                echo "CCY Not Found";
                SendMail("CCY Not Found","2.1 Please input new CCY {$dataCCYsite['CCY']}");
            }    
            while($data=mysqli_fetch_array($resultSetPrice))
            {
                $resultPriceList=mysqli_query($conn,"SELECT  HIGH,LOW,CURRENT FROM tbl_PriceList WHERE CCY='{$dataCCYsite['CCY']}' ORDER BY CreatedDate DESC Limit 1");

                while ($dataPriceList=mysqli_fetch_array($resultPriceList))
                {
                    $CurrentTopOne=$dataPriceList['CURRENT'];
                    if($dataPriceList['CURRENT']>=$data['PriceMin'] &&  $dataPriceList['CURRENT'] <= $data['PriceMax'])
                    {
                        SendMail("Price On Range {$dataCCYsite['CCY']}","Price Min : {$data['PriceMin']} | Price Max : {$data['PriceMax']} | Price Current :         {$dataPriceList['CURRENT']}");
                    }
                    else
                    {
                        $resultPriceListTopFive=mysqli_query($conn,"SELECT HIGH,LOW,CURRENT  FROM tbl_PriceList WHERE CCY='{$dataCCYsite['CCY']}' ORDER BY CreatedDate DESC Limit 5");
                        while ($dataPriceListTopFive=mysqli_fetch_array($resultPriceListTopFive))
                        {
                             $CurrentTopFive=$dataPriceListTopFive['CURRENT'];
                        }
                        
                        if(($CurrentTopFive- $CurrentTopOne)<=$data['PriceRange']) 
                        {
                            SendMail("Price Update {$dataCCYsite['CCY']}","Price Min : {$data['PriceMin']} | Price Max : {$data['PriceMax']} | Price Current :         {$dataPriceList['CURRENT']}");
                        } 
                    }
                 }
            }
        }
        else
        {
        	SendMail("INSERT ERROR","1.1 System Error, Call Administrator");
        }
    }

}
catch (Exception $e) 
{
  SendMail("INSERT ERROR","1.1 System Error, Call Administrator");
}

?>
</body>
</html>