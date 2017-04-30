function deleteHorsForfait(idHorsForfait, libelle, montant) {
    if(confirm("Etes vous sur de vouloir supprimer l'hors forfait "+ libelle +" de "+ montant +" euros ?")) {
        window.location.replace("supprimerUnHorsForfait/"+idHorsForfait) ;
    }
}


