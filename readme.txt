=== Gestion Festival Auteur ===
Contributors: skida
Tags: festival, authors, access
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later

Socle de plugin pour permettre aux auteurs de gerer leur festival apres abonnement.

== Description ==

Gestion Festival Auteur fournira les outils necessaires pour donner aux auteurs un acces controle a la gestion de leur festival.

Le workflow prevu est le suivant:

1. Le festival est cree et valide par l administrateur.
2. L organisateur reclame le droit de modifier le festival.
3. L administrateur accepte ou refuse la demande.
4. La modification reste reservee aux organisateurs valides avec abonnement annuel actif.

Shortcodes disponibles:

* `[foa_claim_button]` affiche le bouton d appel a l action sur une fiche festival.
* `[foa_claim_festival]` affiche le formulaire sur une page separee de reclamation.
* `[foa_my_festival]` affiche l espace organisateur.

== Installation ==

1. Copier le dossier du plugin dans `wp-content/plugins`.
2. Activer le plugin depuis l'administration WordPress.
3. Ouvrir le menu "Gestion Festival".

== Changelog ==

= 0.1.0 =
* Ajout du socle initial du plugin.
* Ajout des demandes de reclamation de festival.
* Ajout de la logique d abonnement annuel actif.
