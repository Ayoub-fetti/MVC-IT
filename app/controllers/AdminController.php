<?php 
require_once (__DIR__.'/../models/User.php');
require_once (__DIR__.'/../models/Categorie.php');

class AdminController extends BaseController {
    private $UserModel;
    private $CategorieModel;

    public function __construct(){
        $this->UserModel = new User();
        $this->CategorieModel = new Categorie();
    }

    public function index() {
        if(!isset($_SESSION['user_loged_in_id'])){
            header("Location: /login ");
            exit;
        }
        $statistics = $this->UserModel->getStatistics();
        $this->renderDashboard('admin/index', ["statistics" => $statistics]);
    }
   
    // public function categories()
    // {
    //     if (!isset($_SESSION['user_id'])) {
    //         header('Location: /admin/login');
    //         exit();
    //     }

    //     $categorieModel = new Categorie();
    //     $categories = $categorieModel->getCategoriesWithSubcategories();
    //     $this->renderDashboard('admin/categories', ['categories' => $categories]);
    // }

    // public function handleCategories()
    // {
    //     if (!isset($_SESSION['user_id'])) {
    //         header('Location: /admin/login');
    //         exit();
    //     }

    //     $categorieModel = new Categorie();

    //     if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //         // Handle category operations
    //         if (isset($_POST["add_modify_category"])) {
    //             $category_name = trim($_POST["category_name_input"]);
    //             $category_id = isset($_POST["category_id_input"]) ? trim($_POST["category_id_input"]) : '';

    //             if (!empty($category_name)) {
    //                 if($category_id == 0) {
    //                     $categorieModel->addCategory($category_name);
    //                 } else {
    //                     $categorieModel->updateCategory($category_id, $category_name);
    //                 }
    //             }
    //         }
            
            // // Handle subcategory operations
            // if (isset($_POST["add_modify_subcategory"])) {
            //     $subcategory_name = trim($_POST["subcategory_name_input"]);
            //     $category_id = $_POST["category_parent_id_input"];
            //     $subcategory_id = (int)trim($_POST["subcategory_id_input"]);

            //     if (!empty($subcategory_name)) {
            //         if($subcategory_id == 0) {
            //             $categorieModel->addSubCategory($subcategory_name, $category_id);
            //         } else {
            //             $categorieModel->updateSubCategory($subcategory_id, $subcategory_name);
            //         }
            //     }
            // }

        //     // Handle category deletion
        //     if (isset($_POST["delete_categorie"])) {
        //         $id_categorie = $_POST['id_categorie'];
        //         $categorieModel->deleteCategory($id_categorie);
        //     }

        //     // Handle subcategory deletion
        //     if (isset($_POST["delete_sub_category"])) {
        //         $id_sous_categorie = $_POST['id_sub_categorie'];
        //         $categorieModel->deleteSubCategory($id_sous_categorie);
        //     }
        // }

    //     header('Location: /admin/categories');
    //     exit();
    // }

    public function testimonials() {
        $this->renderDashboard('admin/testimonials');
    }

    public function projects() {
        $this->renderDashboard('admin/projects');
    }

    public function handleUsers(){
        // Get filter and search values from GET
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; 
        $userToSearch = isset($_GET['userToSearch']) ? $_GET['userToSearch'] : ''; 

        // Call showUsers with both filter and search term
        $users = $this->UserModel->getAllUsers($filter, $userToSearch);
        $this->renderDashboard('admin/users',["users"=> $users]);
    }

    public function removeUser() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_user'])) {
            $userId = $_POST['remove_user'];
            
            if ($this->UserModel->removeUser($userId)) {
                header("Location: /admin/users?message=User successfully deleted");
                exit;
            } else {
                header("Location: /admin/users?error=Failed to delete user");
                exit;
            }
        }
        header("Location: /admin/users");
        exit;
    }

    public function changeStatus(){
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['block_user_id'])) {
            $idUser = $_POST['block_user_id'];
            
            error_log("Attempting to change status for user ID: " . $idUser);
            
            if ($this->UserModel->changeStatus($idUser)) {
                header("Location: /admin/users?message=User status changed successfully");
                exit; 
            } else {
                error_log("Failed to change status for user ID: " . $idUser);
                header("Location: /admin/users?error=Failed to change Status");
                exit;
            }
        }
        error_log("Invalid request to change user status");
        header("Location: /admin/users");
        exit;
    }
}