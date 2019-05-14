<?php

function get_file($url, $local_path, $newfilename)
{
    $err_msg = '';
    echo "Downloading $url";
    echo "\n";
    $out = fopen($local_path.$newfilename,"wrxb");
    if ($out == FALSE){
      print "File not opened.<br>";
      exit;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_FILE, $out);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_exec($ch);

    curl_close($ch);
    //fclose($handle);

}//end function

echo "Starting d324-project updater!\n";

$path = getcwd()."/composer.json";
if(!file_exists($path)){
  echo "\n";
  echo "Please run this command from your d324-project root directory";
  echo "\n";
  exit;
}
$string = file_get_contents(getcwd()."/composer.json");
$json=json_decode($string,true);

if(isset($json["name"]) && $json["name"] != "t324inc/d324-project") {
  echo "\n";
  echo "Please run this command from your d324-project root directory";
  echo "\n";
  exit;
}

if(!isset($json["name"])){
  echo "\n";
  echo "Please run this command from your d324-project root directory";
  echo "\n";
  exit;
}

if(!isset($json["autoload"])){
  $json["autoload"] = [
    "psr-4" => [
      "D324\\composer\\" => "scripts/composer"
    ]
  ];
}else if(isset($json["autoload"]["psr-4"])){
  $json["autoload"]["psr-4"]["D324\\composer\\"] = "scripts/composer";
}else{
  $json["autoload"]["psr-4"] = [
    "D324\\composer\\" => "scripts/composer"
  ];
}

if(!isset($json["scripts"])){
  $json["scripts"] = [
    "d324-composer-generate" => [
      "D324\\composer\\D324Update::generate"
    ]
  ];
}else if(isset($json["scripts"])){
  $json["scripts"]["d324-composer-generate"]= [
    "D324\\composer\\D324Update::generate"
  ];
}
$drupalPath = "web";

echo "Drupal root set to " . $drupalPath . " if your Drupal root is different than this, please change install-path inside your composer.json under the 'extra' section.\n";

if(!isset($json["extra"])){
  $json["extra"] = [
    "install-path" => $drupalPath
  ];
}else{
  $json["extra"]["install-path"] = $drupalPath;
}

$jsondata = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);


if (!file_exists(getcwd().'/scripts/composer')) {
    mkdir(getcwd().'/scripts/composer', 0777, true);
}

if (!file_exists(getcwd().'/scripts/update')) {
    mkdir(getcwd().'/scripts/update', 0777, true);
}

if (!file_exists(getcwd().'/drush')) {
    mkdir(getcwd().'/drush', 0777, true);
}

if (!file_exists(getcwd().'/bin')) {
    mkdir(getcwd().'/bin', 0777, true);
}
$base_path = "https://raw.githubusercontent.com/t324inc/d324-updater/master/";
get_file($base_path . "scripts/composer/D324Update.php", getcwd().'/scripts/composer/', 'D324Update.php');
get_file($base_path . "scripts/update/update-d324.sh", getcwd().'/scripts/update/', 'update-d324.sh');
get_file($base_path . "scripts/update/version-check.php", getcwd().'/scripts/update/', 'version-check.php');
get_file($base_path . "scripts/update/update-config.json", getcwd().'/scripts/update/', 'update-config.json');
//only download them if they don't exist
if (!file_exists(getcwd().'/drush/policy.drush.inc')) {
    get_file($base_path . "drush/policy.drush.inc", getcwd().'/drush/', 'policy.drush.inc');
}
if (!file_exists(getcwd().'/drush/README.md')) {
    get_file($base_path . "drush/README.md", getcwd().'/drush/', 'README.md');
}

chmod(getcwd().'/scripts/update/update-d324.sh', 0755);
chmod(getcwd().'/scripts/update/version-check.php', 0755);
chmod(getcwd().'/scripts/composer/D324Update.php', 0755);

if(file_put_contents($path, $jsondata)) {
  echo "d324-project successfully updated.\n";
  echo "Now you can run ./scripts/update/update-d324.sh to update D324 to the latest version.\n";
  echo "Enjoy!\n";
}else{
  echo "Error while updating d324-project.\n";
  echo ":(\n";
}
