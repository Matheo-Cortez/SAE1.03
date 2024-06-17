#!/usr/bin/php
<?php
$file = file("DOC_UTILISATEUR.md");
file_put_contents('test.html','');

$listeOuverte = false;
$codeOuverte = false;
$tableauOuvert = false;



// Expression régulière pour extraire le texte encadré par des étoiles
$ItaliqueGras = '/\*{1,3}(.*?)\*{1,3}/'; 

file_put_contents("test.html", "<!DOCTYPE html>\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\">\n<head>\n    <meta charset=\"utf-8\" />\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>DOC_UTILISATEUR.md</title>\n    <link rel=\"stylesheet\" href=\"style.css\">\n</head>\n  <body>\n", FILE_APPEND); 

foreach($file as $numLigne => $ligne ){

    $titre ='';
    $liste = '';
    $code = '';
    $lien='';
    $tableau='';

# ***************** TITRES *****************
    # Titre H4
    if((strpos($ligne, "#### ")===0)){
        $titre = ltrim($ligne, $characters = "#### ");
        $titre = trim($titre,"\n");
        file_put_contents("test.html","    <h4>$titre</h4>\n", FILE_APPEND);
    }

    # Titre H3
    elseif((strpos($ligne, "### ")===0)){
        $titre = ltrim($ligne, $characters = "### ");
        $titre = trim($titre,"\n");
        file_put_contents("test.html","    <h3>$titre</h3>\n", FILE_APPEND);
    }

    # Titre H2
    elseif((strpos($ligne, "## ")===0)){
        $titre = ltrim($ligne, $characters = "## ");
        $titre = trim($titre,"\n");
        file_put_contents("test.html","    <h2>$titre</h2>\n", FILE_APPEND);
    }

    # Titre H1
    elseif((strpos($ligne, "# ")===0)){
        $titre = ltrim($ligne, $characters = "# ");
        $titre = trim($titre,"\n");
        file_put_contents("test.html","    <h1>$titre</h1>\n", FILE_APPEND);
    }

# ***************** LISTES *****************

    elseif((strpos($ligne, "- ")===0)) {
        $liste = ltrim($ligne, "- ");
        $liste = trim($liste,"\n");

        // Vérifier si la liste est ouverte
        if (!$listeOuverte) { 
            file_put_contents("test.html","    <ul>\n", FILE_APPEND);
            $listeOuverte = true;
            if (preg_match_all($ItaliqueGras, $liste, $matches)) {
                // $matches[1] contient le texte entre les étoiles
                foreach ($matches[1] as $texteEntreEtoiles) {
                    $texteGrasItalique = '<strong><em>' . $texteEntreEtoiles . '</em></strong>';
                    $texteGras = '<strong>' . $texteEntreEtoiles . '</strong>';
                    $texteItalique = '<em>' . $texteEntreEtoiles . '</em>';
                    
                    $liste = str_replace('***' . $texteEntreEtoiles . '***', $texteGrasItalique, $liste);
                    $liste = str_replace('**' . $texteEntreEtoiles . '**', $texteGras, $liste);
                    $liste = str_replace('*' . $texteEntreEtoiles . '*', $texteItalique, $liste);  
                }
                
            }
            file_put_contents("test.html","      <li>$liste</li>\n", FILE_APPEND);
        }
        elseif($listeOuverte){
            $liste = ltrim($ligne, "- ");
            $liste = trim($liste,"\n");
            if (preg_match_all($ItaliqueGras, $liste, $matches)) {
                // $matches[1] contient le texte entre les étoiles
                foreach ($matches[1] as $texteEntreEtoiles) {
                    $texteGrasItalique = '<strong><em>' . $texteEntreEtoiles . '</em></strong>';
                    $texteGras = '<strong>' . $texteEntreEtoiles . '</strong>';
                    $texteItalique = '<em>' . $texteEntreEtoiles . '</em>';
                    
                    $liste = str_replace('***' . $texteEntreEtoiles . '***', $texteGrasItalique, $liste);
                    $liste = str_replace('**' . $texteEntreEtoiles . '**', $texteGras, $liste);
                    $liste = str_replace('*' . $texteEntreEtoiles . '*', $texteItalique, $liste);  
                }
                
            }
            file_put_contents("test.html","      <li>$liste</li>\n", FILE_APPEND);
        }
    }
        // Fermer la liste si elle est ouverte
    elseif((!(strpos($ligne, "- ")===0))&& ($listeOuverte)){
        file_put_contents("test.html","    </ul>\n", FILE_APPEND);
        $listeOuverte = false;
    }


    # ***************** BLOC CODE *****************

    elseif ((strpos($ligne, "```")===0)&&(!$codeOuverte)) {
            $codeOuverte = true;
            file_put_contents("test.html", "    <pre>\n      <code>\n", FILE_APPEND);
    }
    elseif (!(strpos($ligne, "```")===0)&&($codeOuverte)){
        $code = $ligne;
        file_put_contents("test.html", htmlspecialchars("        $code"), FILE_APPEND);
    }
    elseif( $codeOuverte && ((strpos($ligne, "```")===0))){
        file_put_contents("test.html","\n      </code>\n    </pre>\n", FILE_APPEND);
        $codeOuverte = false;
    }

    elseif(preg_match('/\`(.*?)\`/', $ligne, $match)){
        $box = $match[1];
        $boxCode = "<code>$box</code>";
        $ligne = str_replace(htmlspecialchars("`$box`"), "$boxCode", $ligne, $count);
        $ligne = trim($ligne,"\n");
        file_put_contents("test.html","    <p>$ligne</p>\n", FILE_APPEND);
    }


    # ***************** LIENS ***************** 
       
    elseif(preg_match('/\[(.*?)\]\((.*?)\)/', $ligne, $match)){
        $nom = $match[1];
        $link = $match[2];
        $lien = "<a href=\"$link\"> $nom </a>\n";
        $ligne = str_replace("[$nom]($link)", "$lien", $ligne, $count);
        $ligne = trim($ligne,"\n");
        file_put_contents("test.html","    <p>$ligne</p>\n", FILE_APPEND);
    }


    # ***************** TABLEAU ***************** 

    elseif ((strpos($ligne, '|') !== false)&&(!$tableauOuvert)){

        file_put_contents("test.html", "    <table>\n", FILE_APPEND);
        $tableauOuvert = true;
        $colonnes = explode('|', $ligne);
        $colonnes = array_filter($colonnes, function($value) {
            return rtrim($value) !==0;
        });
        $col = array();
    
        for ($i = 1; $i < count($colonnes)-1; $i++) {
            $col[$i+1] = trim($colonnes[$i]);
        }
    
        print_r($col); 
    
        $ligne = "      <tr>\n        <th>" . implode("</th>\n        <th>", $col) . "</th>\n      </tr>";
        file_put_contents("test.html", "$ligne\n", FILE_APPEND);
    }
    elseif ((strpos($ligne, '|') !== false)&&($tableauOuvert)) {

        if((!preg_match('/\|(\-*?)\|/',$ligne))){
            $colonnes = explode('|', $ligne);
            $colonnes = array_filter($colonnes, function($value) {
                return rtrim($value) !==0;
            });
            $col = array();
        
            for ($i = 1; $i < count($colonnes)-1; $i++) {
                $col[$i+1] = trim($colonnes[$i]);
            }
        
            print_r($col); 
        
            $ligne = "      <tr>\n        <td>" . implode("</td>\n        <td>", $col) . "</td>\n      </tr>";
            file_put_contents("test.html", "$ligne\n", FILE_APPEND);
            }
            
    }
    elseif (!(strpos($ligne, '|'))&&($tableauOuvert)){
        file_put_contents("test.html", "    </table>\n\n", FILE_APPEND);
        $tableauOuvert = false;
    }

        
    # ************* GRAS,ITALIC,GRASITALIC ***********

    elseif (preg_match_all($ItaliqueGras, $ligne, $matches)) {
        // $matches[1] contient le texte entre les étoiles
        foreach ($matches[1] as $texteEntreEtoiles) {
            $texteGrasItalique = '<strong><em>' . $texteEntreEtoiles . '</em></strong>';
            $texteGras = '<strong>' . $texteEntreEtoiles . '</strong>';
            $texteItalique = '<em>' . $texteEntreEtoiles . '</em>';
            
            $ligne = str_replace('***' . $texteEntreEtoiles . '***', $texteGrasItalique, $ligne);
            $ligne = str_replace('**' . $texteEntreEtoiles . '**', $texteGras, $ligne);
            $ligne = str_replace('*' . $texteEntreEtoiles . '*', $texteItalique, $ligne);  
        }
        $ligne = trim($ligne,"\n");
        file_put_contents("test.html","    <p>$ligne</p>\n", FILE_APPEND);
    }


    # ************* PARAGRAPHES,SPAN ***********
    else{
        if(rtrim($ligne,"\n")){
            $ligne = trim($ligne,"\n");
            file_put_contents("test.html","    <p>$ligne</p>\n", FILE_APPEND);

        }
    }

}
file_put_contents("test.html","  </body>\n</html>", FILE_APPEND);

?>