<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSu</title>

    <!-- Your Stylesheets -->
    <link rel="stylesheet" href="/styles/stylee.css">

    <!-- Light/Dark Mode CSS -->
    <style>
    /* Light/Dark Mode */
    body.light-mode {
        background-color: #f8f1e4;
        color: #000;
    }

    body.dark-mode {
        background-color: #2b2b2b;
        color: #fff;
    }

    .sidebar.light-mode {
        background-color: #ffffff;
        color: #000;
    }

    .sidebar.dark-mode {
        background-color: #2b2b2b;
        color: #fff;
    }

    .nav-link.light-mode {
        background: #a47148;
        color: #000;
    }

    .nav-link.dark-mode {
        background: #444;
        color: #fff;
    }

    .nav-link:hover {
        background: #8b5e34;
    }

    </style>

    <!-- Light/Dark Mode JavaScript -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Check and apply the saved mode from localStorage
        if (localStorage.getItem("mode") === "dark") {
            document.body.classList.add("dark-mode");
            document.body.classList.remove("light-mode");
        } else {
            document.body.classList.add("light-mode");
            document.body.classList.remove("dark-mode");
        }

        // Mode toggle functionality
        document.getElementById("light-dark-toggle")?.addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            document.body.classList.toggle("light-mode");

            // Save the mode to localStorage
            var mode = document.body.classList.contains("dark-mode") ? "dark" : "light";
            localStorage.setItem("mode", mode);
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("click", function(e) {
    const el = e.target.closest("[data-confirm]");
    if (!el) return;

    const message = el.getAttribute("data-confirm");
    e.preventDefault();

    // Find the closest modal for this button
    const parentModal = el.closest('.modal-overlay');
    if (parentModal) parentModal.style.display = 'none';

    Swal.fire({
        title: "Please Confirm",
        text: message,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        zIndex: 99999
    }).then(result => {
        if (!result.isConfirmed) {
            // Restore only this modal
            if (parentModal) parentModal.style.display = 'flex';
        } else {
            // If clicking a link
            if (el.tagName === "A") {
                window.location = el.href;
            }
            // If clicking a submit button
            else if (el.closest("form")) {
                el.closest("form").submit();
            }
        }
    });
});


</script>


</head>
