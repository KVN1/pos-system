<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/models/CategoryModel.php'; // Absolute path to CategoryModel

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    public function categories() {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/categories'; // Absolute path to categories view
    }

    public function index() {
        return $this->categoryModel->getCategories();
    }

public function create() {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["category_name"])) {
        $categoryName = $_POST["category_name"];
        $this->categoryModel->addCategory($categoryName);

        // Return JSON success response
        echo json_encode([
            "success" => true,
            "message" => "Category '$categoryName' added successfully!"
        ]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "Invalid request!"]);
    exit;
}



    public function update() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/models/CategoryModel.php';
    $categoryModel = new CategoryModel();

    if ($_POST["action"] === "edit" && !empty($_POST["id"]) && !empty($_POST["category_name"])) {
        $id = intval($_POST["id"]);
        $categoryName = htmlspecialchars($_POST["category_name"]);

        if ($categoryModel->updateCategory($id, $categoryName)) {
            header("Location: /Categories"); // Redirect back after successful update
            exit();
        } else {
            echo "Error updating category.";
        }
    }


        }
    }

    public function delete() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $this->categoryModel->deleteCategory($_POST["id"]);
            header("Location: " . $_SERVER['DOCUMENT_ROOT'] . '/categories'); // Absolute redirect
            exit;
        }
    }
}

// Handle requests
$categoryController = new CategoryController();
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $categoryController->create();
    } elseif ($_POST['action'] === 'edit') {
        $categoryController->update();
    } elseif ($_POST['action'] === 'delete') {
        $categoryController->delete();
    }
}
