<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WebController extends Controller
{
    public function getData(){
        $client = new \GuzzleHttp\Client();

$allLink=[];
for($p=0;$p<27;$p++){
$response = $client->post('https://www.kenjin.ne.jp/ten/2220/2220.asp',[
    'form_params' => [
        'page' => $p,
        'keyword' => '(株)'
         ]
]);


$body=$response->getbody();

$data = mb_convert_encoding($body->getContents(), "UTF-8" , "SJIS");
 $data = strip_tags($data,'<a>');
 //echo $data;
 preg_match_all("/\<\w[^<>]*?\>([^<>]+?\<\/\w+?\>)?|\<\/\w+?\>/i", $data, $matches);
 echo "<pre>";

 foreach($matches[0] as $one){
    if (strpos($one, 'CODE=') !== false) {
        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $one, $a);
        if(!empty($a)){
            $real=str_replace("a.asp","z.asp",$a['href'][0]);
            array_push($allLink,$real);

        }
    }

 }
}
    $links=
    [
        "/ten/2210/2210z.asp?CODE=2955",
        "/ten/2210/2210z.asp?CODE=2385",
        "/ten/2210/2210z.asp?CODE=3419",
        "/ten/2210/2210z.asp?CODE=2393",
        "/ten/2210/2210z.asp?CODE=2406",
        "/ten/2210/2210z.asp?CODE=1020",
        "/ten/2210/2210z.asp?CODE=1009",
        "/ten/2210/2210z.asp?CODE=1838",
        "/ten/2210/2210z.asp?CODE=1848",
        "/ten/2210/2210z.asp?CODE=3661",
        "/ten/2210/2210z.asp?CODE=2448",
        "/ten/2210/2210z.asp?CODE=2784",
        "/ten/2210/2210z.asp?CODE=1856",
        "/ten/2210/2210z.asp?CODE=1029",
        "/ten/2210/2210z.asp?CODE=2463",
        "/ten/2210/2210z.asp?CODE=2467",
        "/ten/2210/2210z.asp?CODE=2470",
        "/ten/2210/2210z.asp?CODE=4830",
        "/ten/2210/2210z.asp?CODE=2486",
        "/ten/2210/2210z.asp?CODE=1123",
        "/ten/2210/2210z.asp?CODE=2496",
    ];
    $data =[];
    //print_r($allLink);
foreach($allLink as $url){
    $item=Self::getPageData($url);
            if($item!=null){
                array_push($data,$item);
            }
        }
  echo "<table>
   <thead>
   <tr>
   <th>Name</th>
       <th>Address</th>
       <th>Phone</th>
       <th>Fax</th>
       <th>E-mail</th>
       <th>Url</th>
       <th>Setup</th>
       <th>Capital</th>
       <th>Represent</th>
       <th>Employee Number</th>
       <th>Profit</th>
       <th>Content</th>
   </tr>
   </thead>
   <tbody>";
   foreach($data as $view){
    if(strpos($view["name"], '株') !== false){
      echo "<tr>
      <td>".$view["name"]."</td>
       <td>".$view["address"]."</td>
       <td>".$view["phone"]."</td>
       <td>".$view["fax"]."</td>
       <td>".$view["email"]."</td>
       <td>".$view["url"]."</td>
       <td>".$view["setup"]."</td>
       <td>".$view["capital"]."</td>
       <td>".$view["represent"]."</td>
       <td>".$view["emplyee_no"]."</td>
       <td>".$view["profit"]."</td>
       <td>".$view["content"]."</td>
       </tr>";
    }
}
   echo "</tbody>
</table>";

//return Excel::download($data, 'export.xls');
}

public function getPageData($url){
    //$url='/ten/2210/2210z.asp?CODE=2385';
    $client = new \GuzzleHttp\Client();

    $response = $client->get('https://www.kenjin.ne.jp/'.$url);

    $body=$response->getbody();

$data = mb_convert_encoding($body->getContents(), "UTF-8" , "SJIS");
 //print_r($data);
// $data = strip_tags($data,"<p><th><td>");
$name=null;
$DOM = new \DOMDocument();
$internalErrors = libxml_use_internal_errors(true);
$DOM->loadHTML($body);
$company = $DOM->getElementsByTagName('p');
$details = $DOM->getElementsByTagName('div');
foreach($company as $node) {
if($node->getAttribute('class')=='Company_name'){
  $name=$node->textContent;
    break;
}

}

foreach($details as $node) {
    $found=null;
    if (strpos($node->textContent, '設立') !== false) {
        $found=strstr($node->textContent, '設立');
        if($found){
            $value=$found;
            $setup = substr($value, 0, strpos($found, "資本金"));
            $value=str_replace($setup,"",$value);
            $setup=str_replace("設立","",$setup);
            $capital = substr($value, 0, strpos($value, "代表者"));
            $value=str_replace($capital,"",$value);
            $capital=str_replace("資本金","",$capital);
            $represent = substr($value, 0, strpos($value, "従業員数"));
            $value=str_replace($represent,"",$value);
            $represent=str_replace("代表者","",$represent);
            $emplyee_no = substr($value, 0, strpos($value, "売上高"));
            $value=str_replace($emplyee_no,"",$value);
            $emplyee_no=str_replace("従業員数","",$emplyee_no);
            $profit = substr($value, 0, strpos($value, "事業内容"));
            $value=str_replace($profit,"",$value);
            $profit=str_replace("売上高","",$profit);
            $content=str_replace("事業内容","",$value);
            $content=substr($content.'展望', 0, strpos($content, '展望'));
           //return $item;

        }
     
    }
    $result=null;
    if (strpos($node->textContent, '所在地') !== false) {
        $result=strstr($node->textContent, '所在地');
        if($result){
            $value=$result;
            $address = substr($value, 0, strpos($result, "電話"));
            $value=str_replace($address,"",$value);
            $address=str_replace("所在地","",$address);
            $phone = substr($value, 0, strpos($value, "FAX"));
            $value=str_replace($phone,"",$value);
            $phone=str_replace("電話","",$phone);
            $fax = substr($value, 0, strpos($value, "E-mail"));
            $value=str_replace($fax,"",$value);
            $fax=str_replace("FAX","",$fax);
            $email = substr($value, 0, strpos($value, "URL"));
            $value=str_replace($email,"",$value);
            $email=str_replace("E-mail","",$email);
            $url=str_replace("URL","",$value);
            $item=[
                'name'=>$name,
                'address'=>$address,
                'phone'=>$phone,
                'fax'=>$fax,
                'email'=>$email,
                'url'=>$url,
                'setup'=>$setup,
                'capital'=>$capital,
                'represent'=>$represent,
                'emplyee_no'=>$emplyee_no,
                'profit'=>$profit,
                'content'=>$content
           ];
           return $item;
        }
    }
    }
return null;
}
}
