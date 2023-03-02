<?php
// guardamos en en variables el token asignado a nuestro bot y la web en de telegram para su uso a posteriori
$token ='6231298737:AAEzetILujRKL4jK3PoHBfCEco9BnsJkRfk';
$website='https://api.telegram.org/bot'.$token;

// recogemos la informacion mandada por el usuario y la codificamos 
$input=file_get_contents('php://input');
$update = json_decode($input, TRUE);

// guardamos en variables el identificador del chat que nos envia el mensaje y el mensaje en si
$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];
//realizamos un swicth en caso de que el usuario ingrese cierto tipo de mensajes
switch ($message) {
    //en el caso de que el mensaje sea /start indicaremos que el bor ha sido iniciado
    case '/start':
        $response = 'Me has iniciado';
        sendMessage($chatId,$response);
        break;
        //en el caso de escribir /info se le da una instruccion de como utilzar el bot 
    case'/info':
        $response= '
            Hola soy @medacbot
            si quieres consultar los datos de algun pokemon
            debes escribir /nombre o numero del pokemon que quieras consultar';
        sendMessage($chatId,$response);
        
        break;
        //en el caso de que el mensaje sea por defecto un nombre de pokemon utilizamos los metodos sendMessage y sendFoto 
    default:
        $responsePhoto=getPokemonPhoto($message);
        $responseText=getPokemonInfo($message);
        sendMessage($chatId,$responseText);
        sendFoto($chatId,$responsePhoto);
        break;
}
//esta funcion se encarga de enviar la informacion de forma escrita atraves del hosting al bot de telegram
function sendMessage($chatId,$response){
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}
//esta funcion se encarga de enviar la imagen deseada atraves del hosting al bot de telegram
function sendFoto($chatId,$response){
    $url = $GLOBALS['website'].'/sendPhoto?chat_id='.$chatId.'&photo='.urlencode($response);
    file_get_contents($url);
}
//esta funcion se encarga de realizar una conexion con la api de pokemon y sacar de este la foto de este pero para ello se le debe paser el nombre o numero de este
function getPokemonPhoto($message){
$api = curl_init("https://pokeapi.co/api/v2/pokemon$message");
curl_setopt($api,CURLOPT_RETURNTRANSFER,true);
$response = curl_exec($api);

$json = json_decode($response);
return $json->sprites->other->dream_world->front_default;
}
//esta funcion se encarga de realizar una conexion con la api de pokemon y sacar de este la la inforamacion del pokemon pero para ello se le debe paser el nombre o numero de este
function getPokemonInfo($message){
$api = curl_init("https://pokeapi.co/api/v2/pokemon$message");
curl_setopt($api,CURLOPT_RETURNTRANSFER,true);
$response = curl_exec($api);

$json = json_decode($response);
$tipos=' ';
        foreach ($json->types as $tipo) {
            $tipos=$tipos.' '. $tipo->type->name;
        }
$info=$json->id.' '.$json->name.' '.$tipos;
return $info;
}

?>