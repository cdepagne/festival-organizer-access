<?php
/**
 * ACF field registry.
 *
 * @package FestivalOrganizerAccess
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Centralizes ACF field names used by the plugin.
 */
class FOA_ACF_Fields
{
    /**
     * Existing festival fields already managed by the site owner.
     *
     * @return array
     */
    public static function existing_fields()
    {
        return array(
            'votre_description',
            'achatbillet',
            'lemois',
            'le-fyer',
            'lieu_du_festival',
            'numero_departement',
            'site_web',
            'contact',
            'page_facebook',
            'billetterie',
            'billet_ticketmaster',
            'billet_fnac',
            'infos_pratiques',
            'video',
            'valide',
        );
    }

    /**
     * Future organizer-editable fields.
     *
     * @return array
     */
    public static function organizer_editable_fields()
    {
        return array(
            'nom_festival',
            'organisateur',
            'email_organisateur',
            'genre_musical',
            'date_debut',
            'date_fin',
            'annee_edition',
            'tarif',
            'gratuit',
            'ville',
            'region',
            'pays',
            'public_festival',
            'accessibilite_pmr',
            'camping',
            'parking',
            'programmation',
            'image_banniere',
            'statut_fiche',
        );
    }

    /**
     * Fields that must remain owner/admin controlled.
     *
     * @return array
     */
    public static function owner_only_fields()
    {
        return array(
            'valide',
            'statut_fiche',
        );
    }
}
