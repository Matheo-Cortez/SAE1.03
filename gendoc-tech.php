<?php
#!/usr/bin/php

function modifVersion($vActuelle, $param) {
  $vNouvelle = explode('.', $vActuelle);

  switch ($param) {
      case '--major':
          $vNouvelle[0] = intval($vNouvelle[0]) + 1;
          $vNouvelle[1] = 0;
          $vNouvelle[2] = 0;
          break;
      case '--minor':
          $vNouvelle[1] = intval($vNouvelle[1]) + 1;
          $vNouvelle[2] = 0;
          break;
      case '--build':
          $vNouvelle[2] = intval($vNouvelle[2]) + 1;
          break;
      default:
          return $vActuelle;
  }

  return implode('.', $vNouvelle);
}

function modifVersionConfig($config, $param) {
  $contenu = file_get_contents($config);

  preg_match('/VERSION=(\d+\.\d+\.\d+)/', $contenu, $temp);
  $vActuelle = $temp[1];

  $vNouvelle = modifVersion($vActuelle, $param);
  $nouveauContenu = "VERSION=" .$vNouvelle;
  $nouveauContenu = preg_replace('/(VERSION=)\d+\.\d+\.\d+/',$nouveauContenu, $contenu);
  file_put_contents($config, $nouveauContenu);

  return $vNouvelle;
}
$options = getopt(null, ['major', 'minor', 'build']);
$param="";

if (isset($options['major'])) {
    $param = '--major';
} elseif (isset($options['minor'])) {
    $param = '--minor';
} elseif (isset($options['build'])) {
    $param = '--build';
}

$config = "config";
$vNouvelle = modifVersionConfig($config, $param);
$config = parse_ini_file('config', true);
  $TabFile = glob('*.c');

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
      <meta charset="utf-8" />
      <title>doc_tech-<?php echo "{$config['VERSION']}"?></title>
  </head>
  <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: black; 
            color: #F2F2F2;
        }
        h1, h2, p {
            margin-bottom: 10px;
        }
        h1, h1+p{
          text-align:center;
        }
    </style>
</head>
<body>
    <?php
    echo "<h1>Documentation Technique</h1>";
    echo "<h2>Nom du Client:</h2><p>{$config['CLIENT']}</p>";
    echo "<h2>Nom du Produit:</h2><p>{$config['PRODUIT']}</p>";
    echo "<h2>Version du Produit:</h2><p>{$config['VERSION']}</p>";
    echo "<h2>Date de Génération:</h2><p>" . date('d-m-Y H:i:s') . "</p>";
    ?>
</body>

<?php

foreach($TabFile as $script){
  $file=file($script);
  $enTete = "";
  $define = [];
  $structure = array();
  $variable = array();
  $fonction = array();
  $bref = "";
  $detail = "";
  $parametre = [];
  $retourne = "";
  $nom = "";
  $inddef =0;
  $indstruc =0;
  $indvar =0;
  $indfn =0;
  $indpara =0;
  $tabIndPara=[];
  $indVarStr=0;
  $tabStr=[];
  $dansStruc = false;
  $dansFn = false;
  foreach($file as $numLigne => $ligne ) {

    $ligne = ltrim($ligne);
    if (str_contains($ligne, 'struct')) {
      $dansStruc = true;
    }
    else if(($dansStruc == true) && str_contains($file[$numLigne-1], '}')){
      $tabStr[$indstruc]=$indVarStr;
      $indVarStr=0;
      $indstruc++;
      $dansStruc = false;
    }

    if(str_contains($ligne, '#define') || str_contains($ligne, '# define')){
        $define[$inddef]=ltrim($ligne, '# define');
        $inddef++;
      }
      
    else if (str_contains($ligne, '/**') && str_contains($ligne, '*/')){
      if($dansStruc==true){
        if(str_contains($ligne, '}'))
          $structure[$indstruc][0]=ltrim($ligne, '}');
        else{
          $structure[$indstruc][1][$indVarStr]=ltrim($ligne);
          $indVarStr++;
        }
      }

      else{
        $variable[$indvar]=ltrim($ligne, ' * \var');
        $indvar++;
      }
    }

//------------FONCTION-----------
    else if (($dansFn == false) && str_contains($ligne, '\\')) {
      $dansFn = true;
    }
    else if(($dansFn == true) && str_contains($ligne, '*/')){
      $nom=rtrim($file[$numLigne+1], '{');
      $fonction[$indfn]=[$nom,$parametre,$retourne,$bref,$detail];
      $tabIndPara[$indfn]=$indpara;
      $indfn++;
      $indpara=0;
      $detail="";
      $bref="";
      $dansFn = false;
    }

    if($dansFn == True){
      if(str_starts_with($ligne, '*')){
        if(str_contains($ligne, ' \brief ')){
          $bref=ltrim($ligne, ' * \brief ');
        }

        else if(str_contains($ligne, ' \detail ')){
          $detail=$detail . ltrim($ligne, '* \detail ');
        }
        else if(str_word_count( $ligne, 0)>1 && !str_contains( $ligne, '\\')){
          $detail=$detail . ltrim($ligne, '* \detail ');
        }

        else if(str_contains($ligne, '\param ')){
          $parametre[$indpara]=ltrim($ligne, ' * \param ');
          $indpara++;
        }
        else if(str_contains($ligne, '\return ')){
          $retourne=ltrim($ligne, '* \return ');
        }
      }

    }
//-----------------------
    else if(str_starts_with( $ligne, '*')){
      if(str_word_count( $ligne, 0)>1 && !str_contains( $ligne, '\\') && !str_contains( $ligne, '/')&& !str_contains( $ligne, ';')){
        $enTete=$enTete . ltrim($ligne, "* ");
      }
    }
  }

?>
    
      <hr>
      <hr>
        <h1><ins>Description du <?php echo $script ?></ins></h1>
      <p><?php echo $enTete?></p>
      <hr>

      <!--- Defines --->

      <h2>Defines</h2>
      <?php for($i=0; $i<$inddef; $i++){
        $def=explode('/**',$define[$i])?>
      <dl>
          <dt><?php echo rtrim($def[0], ";")?></dt>
          <dd><?php echo rtrim(rtrim($def[1]), "*/")?></dd>
      </dl>

      <br>
      <?php }?>

      <!--- Structures -------------------->
      <h2>Structures</h2>
      <?php for($i=0; $i<$indstruc; $i++){
        $str=explode('/**',$structure[$i][0])?>
      <dl>
          <dt><?php echo rtrim($str[0])?></dt>
          <dd>
              <dl>
                  <dt><?php echo rtrim(rtrim($str[1]), "*/")?></dt>
                  <ul>
                      <?php for($j=0; $j<$tabStr[$i]; $j++){?>
                      <li><?php echo $structure[$i][1][$j]?></li>
                      <?php }?>
                  </ul>
              </dl>
          </dd>
      </dl>

      <br>
      <?php }?>

      <!--- Variables Globales --->
      <h2>Variables Globales</h2>
      <?php for($i=0; $i<$indvar; $i++){
        $var=explode('/**',$variable[$i])?>
      <dl>
          <dt><?php echo rtrim($var[0])?></dt>
          <dd><?php echo rtrim(rtrim($var[1]), "*/")?></dd>
      </dl>

      <br>
      <?php }?>

      <!--- Fonctions et Procédure --->
      <h2>Fonctions et Procédure</h2>
      <?php for($i=0; $i<$indfn; $i++){?>
      <dl>
          <dt><?php echo rtrim(rtrim($fonction[$i][0]),"{")?></dt>
          <dd>
              <dl>
                  <dt><?php echo rtrim($fonction[$i][3])?></dt>
                  <dd>
                      <dl><?php if(!empty($fonction[$i][1])){ ?>
                          <dt>Paramètre :</dt>  
                          <ul>
                            <?php for($j=0; $j<$tabIndPara[$i]; $j++){?>
                              <li><?php echo $fonction[$i][1][$j]?></li>
                            <?php }?>
                          </ul>
                          <?php }?>
                          <?php if ($fonction[$i][2]!=""){ ?>
                          <dt>Renvoie :</dt>
                          <dd><?php echo rtrim($fonction[$i][2])?></dd>
                          <?php }?>
                      </dl>
                  </dd>
                  <dt><?php echo rtrim($fonction[$i][4])?></dt>
              </dl>    
          </dd>
      </dl> 
      <br>
      <?php }?>

      </body>
      </html>
<?php } ?>