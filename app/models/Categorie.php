<?php
require_once(__DIR__.'/../config/db.php');

class Categorie extends Db {
    public function __construct() {
        parent::__construct();
    }

    public function getCategoriesWithSubcategories() {
        try {
            $query = $this->conn->prepare("
                SELECT 
                    c.id_categorie,
                    c.nom_categorie,
                    sc.id_sous_categorie,
                    sc.nom_sous_categorie
                FROM 
                    categories c
                LEFT JOIN 
                    sous_categories sc ON c.id_categorie = sc.id_categorie
                ORDER BY 
                    c.nom_categorie, sc.nom_sous_categorie
            ");
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
    
            $categories = [];
            foreach ($results as $row) {
                $id_categorie = $row['id_categorie'];
    
                if (!isset($categories[$id_categorie])) {
                    $categories[$id_categorie] = [
                        'id_categorie' => $id_categorie,
                        'nom_categorie' => $row['nom_categorie'],
                        'sous_categories' => []
                    ];
                }
    
                if (!empty($row['id_sous_categorie'])) {
                    $categories[$id_categorie]['sous_categories'][] = [
                        'id_sous_categorie' => $row['id_sous_categorie'],
                        'nom_sous_categorie' => $row['nom_sous_categorie']
                    ];
                }
            }
    
            return $categories;
    
        } catch (PDOException $e) {
            error_log("Error getting categories with subcategories: " . $e->getMessage());
            return [];
        }
    }

    public function addCategory($name) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO categories (nom_categorie) VALUES (?)");
            return $stmt->execute([$name]);
        } catch (PDOException $e) {
            error_log("Error adding category: " . $e->getMessage());
            return false;
        }
    }

    public function updateCategory($id, $name) {
        try {
            $stmt = $this->conn->prepare("UPDATE categories SET nom_categorie = ? WHERE id_categorie = ?");
            return $stmt->execute([$name, $id]);
        } catch (PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCategory($id) {
        try {
            // First delete all subcategories
            $stmt = $this->conn->prepare("DELETE FROM sous_categories WHERE id_categorie = ?");
            $stmt->execute([$id]);

            // Then delete the category
            $stmt = $this->conn->prepare("DELETE FROM categories WHERE id_categorie = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    public function addSubCategory($name, $categoryId) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO sous_categories (nom_sous_categorie, id_categorie) VALUES (?, ?)");
            return $stmt->execute([$name, $categoryId]);
        } catch (PDOException $e) {
            error_log("Error adding subcategory: " . $e->getMessage());
            return false;
        }
    }

    public function updateSubCategory($id, $name) {
        try {
            $stmt = $this->conn->prepare("UPDATE sous_categories SET nom_sous_categorie = ? WHERE id_sous_categorie = ?");
            return $stmt->execute([$name, $id]);
        } catch (PDOException $e) {
            error_log("Error updating subcategory: " . $e->getMessage());
            return false;
        }
    }

    public function deleteSubCategory($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM sous_categories WHERE id_sous_categorie = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting subcategory: " . $e->getMessage());
            return false;
        }
    }
}
