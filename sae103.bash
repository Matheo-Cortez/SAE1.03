#!/usr/bin/bash

#docker image pull sae103-php 
#docker image pull sae103-html2pdf
#docker image pull clock

# Création du volume sae103
docker volume create sae103

# Lancement d’un conteneur clock en mode détaché, en lui donnant le nom sae103-forever et en lui montant le volume sae103 
docker container run --name clock sae103-forever -v sae103:/work -d clock 

# Copie des fichiers .c dans le volume sae103 en utilisant sae103-forever comme conteneur cible.
docker container cp src1.c sae103-forever:/work/
docker container cp src2.c sae103-forever:/work/
docker container cp src3.c sae103-forever:/work/
docker container cp gendoc-user.php sae103-forever:/work/
docker container cp gendoc-tech.php sae103-forever:/work/

# Lancement de tous les autres traitements les uns après les autres, mais en mode non interactif cette fois-ci
#PHP to HTML :
docker container run --rm -v sae103:/work/ sae103-php php /work/gendoc-tech.php
docker container run --rm -v sae103:/work/ sae103-php php /work/gendoc-user.php

#HTML to PDF:
docker container run --rm -v sae103:/work/ sae103-html2pdf 'html2pdf src1.html src1.pdf'
 
# Récupération de l’archive finale depuis le volume sae103, en utilisant encore sae103-forever comme conteneur source cette fois-ci.
docker container cp sae103-forever:/work/ ./RenduSAE103
mkdir ./FinalSAE103
mv RenduSAE103/*.pdf ./FinalSAE103
rm RenduSAE103/*
rmdir RenduSAE103/
tar -czvf SAE103.tar.gz FinalSAE103/
rm FinalSAE103/*
rmdir FinalSAE103/
# Vérification :
# echo "Fichiers :"
# docker container exec sae103-forever ls work/
# echo " "

# Arrêt du conteneur sae103-forever
docker container stop sae103-forever
docker container rm sae103-forever
docker volume prune 

# Suppression du volume sae103
docker volume rm sae103

# echo "Programme fini"
