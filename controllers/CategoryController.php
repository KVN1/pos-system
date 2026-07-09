<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../controllers/ActivityLogController.php';

class CategoryController {
    private $categoryModel;
    private $activityLog;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
        $this->activityLog = new ActivityLogController();
    }

    // Get active products under a category (AJAX)
    public function getProductsByCategory() {
        if (isset($_GET['category_name'])) {
            $categoryName = $_GET['category_name'];
            $products = $this->categoryModel->getProductsByCategoryName($categoryName);
            echo json_encode($products);
            exit;
        }
    }

    // Show active categories page
    public function categories() {
        $categories = $this->categoryModel->getActiveCategories();
        require_once(__DIR__ . '/../views/categories.php');
    }

    // Fetch active categories
    public function index() {
        return $this->categoryModel->getActiveCategories();
    }

    // Add new category (AJAX)
    public function create() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["category_name"])) {
            $categoryName = htmlspecialchars($_POST["category_name"]);

            if ($this->categoryModel->addCategory($categoryName)) {
                if (isset($_SESSION['user_id'])) {
                    $this->activityLog->addLog($_SESSION['user_id'], 'Add Category', "Added category: $categoryName");
                }
                echo json_encode([
                    "success" => true,
                    "message" => "Category '$categoryName' added successfully!"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to add category."
                ]);
            }
            exit;
        }
        echo json_encode(["success" => false, "message" => "Invalid request!"]);
        exit;
    }

    // Edit category
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["id"]) && !empty($_POST["category_name"])) {
            $id = intval($_POST["id"]);
            $categoryName = htmlspecialchars($_POST["category_name"]);

            if ($this->categoryModel->updateCategory($id, $categoryName)) {
                if (isset($_SESSION['user_id'])) {
                    $this->activityLog->addLog($_SESSION['user_id'], 'Edit Category', "Edited category ID $id to: $categoryName");
                }
                $_SESSION['flash_message'] = "Category '$categoryName' updated successfully!";
                $_SESSION['flash_type'] = "success";
                header("Location: /Categories");
                exit;
            } else {
                $_SESSION['flash_message'] = "Failed to update category.";
                $_SESSION['flash_type'] = "error";
                header("Location: /Categories");
                exit;
            }
        }
    }

    // Deactivate category
    public function deactivate() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $id = intval($_POST["id"]);
            if ($this->categoryModel->deactivateCategory($id)) {
                if (isset($_SESSION['user_id'])) {
                    $this->activityLog->addLog($_SESSION['user_id'], 'Deactivate Category', "Deactivated category ID $id");
                }
                $_SESSION['flash_message'] = "Category deactivated successfully!";
                $_SESSION['flash_type'] = "success";
                header("Location: /Categories");
                exit;
            } else {
                $_SESSION['flash_message'] = "Failed to deactivate category.";
                $_SESSION['flash_type'] = "error";
                header("Location: /Categories");
                exit;
            }
        }
    }

    // Restore category
    public function restore() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $id = intval($_POST["id"]);
            if ($this->categoryModel->restoreCategory($id)) {
                if (isset($_SESSION['user_id'])) {
                    $this->activityLog->addLog($_SESSION['user_id'], 'Restore Category', "Restored category ID $id");
                }
                $_SESSION['flash_message'] = "Category restored successfully!";
                $_SESSION['flash_type'] = "success";
                header("Location: /views/settings.php");
                exit;
            } else {
                $_SESSION['flash_message'] = "Failed to restore category.";
                $_SESSION['flash_type'] = "error";
                header("Location: /views/settings.php");
                exit;
            }
        }
    }

    // Get deactivated categories
    public function getDeactivated() {
        return $this->categoryModel->getDeactivatedCategories();
    }
}

// Handle POST requests
$categoryController = new CategoryController();

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $categoryController->create();
            break;
        case 'edit':
            $categoryController->update();
            break;
        case 'delete':
            // Optional: implement delete if needed
            break;
        case 'deactivate':
            $categoryController->deactivate();
            break;
        case 'restore':
            $categoryController->restore();
            break;
    }
}

// Handle AJAX GET request for products
if (isset($_GET['action']) && $_GET['action'] === 'getProducts' && isset($_GET['category_name'])) {
    $categoryController->getProductsByCategory();
}

?>
