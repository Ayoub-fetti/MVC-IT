<?php
  require_once(__DIR__.'/../config/db.php');

  class Testimonials extends Db {
    public function __construct() {
        parent::__construct();
    }
}


     function getClientTestimonials($conn, $role, $userId = null) {
        // Base query
        $queryStr = "SELECT p.titre_projet, t.commentaire, t.id_temoignage, o.montant, o.delai, o.id_offre
                    FROM temoignages t
                    JOIN offres o ON t.id_offre = o.id_offre
                    JOIN projets p ON o.id_projet = p.id_projet";

        // Modify query based on the role
        $params = [];
        if ($role === 'Freelancer') {
            $queryStr .= " WHERE o.id_utilisateur = ?";
            $params[] = $userId;
        } elseif ($role === 'Client') {
            $queryStr .= " WHERE p.id_utilisateur = ?";
            $params[] = $userId;
        }
        // Admin has no additional conditions, so no modification to the query

        // Prepare and execute the query
        $query = $conn->prepare($queryStr);
        $query->execute($params);

        // Fetch and return results
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Determine role based on the file name
    $scriptName = basename($_SERVER['SCRIPT_NAME']);
    $role = '';
    if (strstr($scriptName, "freelancer")) {
        $role = 'Freelancer';
    } elseif (strstr($scriptName, "client")) {
        $role = 'Client';
    } elseif (strstr($scriptName, "admin")) {
        $role = 'Admin';
    }

    // Fetch the testimonials
    $userId = $_SESSION['user_loged_in_id'] ?? null; // Use null for Admin
    $clientTestimonials = getClientTestimonials($conn, $role, $userId);
?>
