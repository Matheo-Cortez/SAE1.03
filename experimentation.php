

<?php
  #!/usr/bin/php
  function read() {
    static $stdin = null;
    if ($stdin === null) {
    $stdin = fopen('php://stdin', 'r');
    }
    return rtrim(fgets($stdin));
  }

  $description = array();
  $define = [];
  $structure = array();
  $variable = array();
  $fonction = array();
  $bref = array();
  $parametre = array();
  $retourne = array();
  $inddes = 0;
  $inddef =0;
  $indstruc =0;
  $indvar =0;
  $indfn =0;
  $indbref =0;
  $indpara =0;
  $indretu =0;
  $test = "";

  echo "Nom de fichier : ";
  $file = file(read());
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
        <meta charset="utf-8" />
      <title>DOC_TECHNIQUE</title>
  </head>

    <body>
        <h1><ins>Description du code c</ins></h1>
<?php


  foreach($file as $numLigne => $ligne ) {

    $ligne = ltrim($ligne);
    if ( str_starts_with($ligne, '*')) {

      if(!(str_contains( $ligne, '\\'))){
        if(str_word_count( $ligne, 0)>4)
          $description[$inddes]=$description[$inddes] . ltrim($ligne, '* ');
        else if(array_key_exists($inddes, $description)) { 
          $inddes++;
        }
      }

      else if(str_contains($ligne, '\def')){
        $define[$inddef]=ltrim($ligne, ' * \def');
        $inddef++;
      }

      else if(str_contains($ligne, '\struct')){
        $structure[$indstruc]=ltrim($ligne, ' * \struct');
        $indstruc++;
      }

      else if(str_contains($ligne, '\var')){
        $variable[$indvar]=ltrim($ligne, ' * \var');
        $indvar++;
      }

      else if(str_contains($ligne, '\fn')){
        $fonction[$indfn]=ltrim($ligne, ' * \fn');
        $test=ltrim($ligne, ' * \fn');
        $indfn++;
      }

      else if(str_contains($ligne, '\brief')){
        $bref[$indbref]=ltrim($ligne, ' * \brief');
        $indbref++;
      }

      else if(str_contains($ligne, '\param')){
        $parametre[$fonction[$indpara]]=ltrim($ligne, ' * \param');
        $indpara++;
      }

      else if(str_contains($ligne, '\return')){
        $retourne[$indretu]=ltrim($ligne, '* \return ');
        $indretu++;
      }
    }
  }

//  print_r($description);
//  print_r($define);
//  print_r($structure);
//  print_r($variable);
//  print_r($fonction);
//  print_r($bref);
//  print_r($parametre);
//  print_r($retourne);
  //file_put_contents('test.html', implode("\n",$fonction));
?>
<p><?php echo $description[0]?></p>
<hr>

<!--- Defines --->

<h2>Defines</h2>
<?php for($i=0; $i<$inddef; $i++){
  ?>
<dl>
    <dt><?php echo $define[$i]?></dt>
    <dd>Aute minim sint minim dolor in veniam dolore minim laborum... </dd>
</dl>

<br>
<?php }?>

<!--- Structures --->
<h2>Structures</h2>
<?php for($i=0; $i<$indstruc; $i++){?>
<dl>
    <dt><?php echo $structure[$i]?></dt>
    <dd>
        <dl>
            <dt>Aute minim sint minim dolor in veniam dolore minim laborum... (déscription de la structure)</dt>
            <ul>
                <li>nom_variable :  Aute minim sint minim (déscription de la variable)</li>
                <li>lorem : Dolor in veniam dolore</li>
            </ul>
        </dl>
    </dd>
</dl>

<br>
<?php }?>

<!--- Variables Globales --->
<h2>Variables Globales</h2>
<?php for($i=0; $i<$indvar; $i++){?>
<dl>
    <dt><?php echo $variable[$i]?></dt>
    <dd>Aute minim sint minim dolor in veniam dolore minim laborum...</dd>
</dl>

<br>
<?php }?>

<!--- Fonctions et Procédure --->
<h2>Fonctions et Procédure</h2>
<?php for($i=0; $i<$indfn; $i++){?>
<dl>
    <dt><?php echo $fonction[$i]?></dt>
    <dd>
        <dl>
            <dt>Ajoute des étudiant·e. Résumé court du rôle de la fonction</dt>
            <dd>
                <dl>
                    <dt>Paramètre :</dt>
                    <ul>
                        <li>nom :[sortie]  Nom de l'étudiant·e</li>
                        <li>groupe_td :[entrée] Son groupe TD</li>
                        <li>num_tp :[entrée] Son numéro de groupe TP</li>
                    </ul>
                    <dt>Renvoie :</dt>
                    <dd>int Numéro d'identité de l'étudiant·e ajouté·e</dd>
                </dl>
            </dd>
            <dt>Un descriptif détaillé de ce que fait la fonction entrant dans les détails de ses caractéristiques, des conditions particulières d'utilisation, faisant apparaître éventuellement : un formatage sommaire, sous forme de listes et de sauts de lignes</dt>
        </dl>    
    </dd>
</dl> 
<br>
<?php }?>

</body>
</html>