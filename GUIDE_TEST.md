# Guide de test - Système de gestion des missions

## ✅ État du système : TOUT EST OK !

La vérification automatique confirme que :
- Toutes les tables de base de données existent
- Toutes les colonnes nécessaires sont présentes
- Tous les modèles sont fonctionnels

---

## 🧪 PROTOCOLE DE TEST - Étape par étape

### 1. **Test des villes pré-enregistrées**

**Objectif** : Vérifier la création et l'utilisation des villes

**Étapes** :
1. Aller dans "Planning missions"
2. Cliquer "Nouvelle réservation"
3. Dans "Destination", cocher "Nouvelle ville"
4. Remplir :
   - Nom : "Paris"
   - Région : "Île-de-France"
   - Destination spécifique : "Siège social"
5. Créer la mission
6. Vérifier que "Paris (Île-de-France)" apparaît dans la liste des villes

**Résultat attendu** : ✅ Ville créée et disponible pour les futures missions

---

### 2. **Test des photos avant/après mission**

**Objectif** : Vérifier l'upload et la gestion des photos

**Étapes** :
1. Créer une nouvelle mission ou modifier une existante
2. Dans la liste des missions, cliquer l'icône 📷 (caméra vide) pour "Photos avant"
3. Uploader 2-3 photos (format JPG/PNG)
4. Fermer le modal et cliquer l'icône 📷 (caméra remplie) pour "Photos après"
5. Uploader d'autres photos
6. Vérifier que les photos s'affichent correctement avec aperçu

**Résultat attendu** :
- ✅ Photos uploadées et visibles
- ✅ Distinction avant/après mission
- ✅ Possibilité de supprimer des photos

---

### 3. **Test des documents de mission**

**Objectif** : Vérifier l'upload de PDF et images

**Étapes** :
1. Dans la liste des missions, cliquer l'icône 📄 "Documents"
2. Sélectionner "Ordre de mission" comme type
3. Uploader un fichier PDF (ou image)
4. Changer le type en "Rapport de mission"
5. Uploader un autre document
6. Vérifier l'affichage avec les types et tailles

**Résultat attendu** :
- ✅ Documents uploadés
- ✅ Catégorisation correcte
- ✅ Aperçu/lien de téléchargement fonctionnel

---

### 4. **Test du rapport Excel**

**Objectif** : Générer un rapport des déplacements

**Étapes** :
1. Cliquer le bouton "Rapport" en haut à droite
2. Sélectionner une période (ex: ce mois)
3. Choisir un demandeur ou laisser "Tous"
4. Sélectionner un statut ou laisser "Tous"
5. Cliquer "Générer le rapport Excel"

**Résultat attendu** :
- ✅ Fichier Excel téléchargé
- ✅ Colonnes : ID, Statut, Demandeur, Service, Chauffeur, Véhicule, Dates, Destination, KM, Notes
- ✅ Données filtrées correctement

---

### 5. **Test d'une mission complète**

**Objectif** : Scénario réel de bout en bout

**Étapes** :
1. **Créer une mission** :
   - Véhicule disponible
   - Chauffeur disponible
   - Demandeur existant ou nouveau
   - Ville pré-enregistrée ou nouvelle
   - Dates et destination

2. **Ajouter des photos avant** (état du véhicule au départ)

3. **Approuver la mission** (si admin)

4. **Marquer comme terminée** avec KM retour

5. **Ajouter des photos après** (constat visuel)

6. **Ajouter des documents** (ordre de mission, rapport)

7. **Générer un rapport** incluant cette mission

**Résultat attendu** : ✅ Workflow complet fonctionnel

---

## 🔍 Points de contrôle pendant les tests

### Interface utilisateur
- [ ] Boutons photos (📷/📷) visibles et fonctionnels
- [ ] Bouton documents (📄) visible et fonctionnel
- [ ] Bouton "Rapport" visible
- [ ] Modals s'ouvrent correctement
- [ ] Upload de fichiers fonctionne

### Données
- [ ] Villes créées sont sauvegardées
- [ ] Photos sont stockées dans `storage/app/public/missions/{id}/photos/`
- [ ] Documents sont stockés dans `storage/app/public/missions/{id}/documents/`
- [ ] Relations base de données correctes

### Fonctionnalités
- [ ] Filtres de rapport fonctionnels
- [ ] Export Excel contient toutes les colonnes
- [ ] Calcul automatique de la distance KM
- [ ] Statuts de mission mis à jour correctement

---

## 🚨 En cas de problème

Si vous rencontrez une erreur :

1. **Vérifiez les logs** : `storage/logs/laravel.log`
2. **Vérifiez les permissions** : Dossier `storage` doit être accessible en écriture
3. **Vérifiez la configuration** : `config/filesystems.php` pour le disque 'public'
4. **Testez avec de petits fichiers** d'abord (photos < 1MB, PDF < 2MB)

---

## ✅ Validation finale

Après avoir testé tous les points ci-dessus, votre système de gestion des missions sera complètement opérationnel avec :

- 📍 Gestion des villes pré-enregistrées
- 📷 Photos avant/après mission pour constat visuel
- 📄 Documents de mission (PDF/Images)
- 📊 Rapports Excel détaillés des déplacements

**Prêt à tester ? Commencez par le test 1 !**