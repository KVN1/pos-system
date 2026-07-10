import os

# Run from D:\xampp\htdocs\POSu
# Fixes ../includes/ relative paths in views

fixed = []
skip = {'.git', 'backups', 'backup', 'vendor'}

for root, dirs, files in os.walk('.'):
    dirs[:] = [d for d in dirs if d not in skip]
    for f in files:
        if not f.endswith('.php'):
            continue
        fp = os.path.join(root, f)
        try:
            with open(fp, 'r', encoding='utf-8', errors='ignore') as fh:
                content = fh.read()
            nc = content

            # Fix relative includes
            nc = nc.replace("include '../includes/sidebar.php'", "include __DIR__ . '/../includes/sidebar.php'")
            nc = nc.replace("include('../includes/sidebar.php')", "include(__DIR__ . '/../includes/sidebar.php')")
            nc = nc.replace("require_once '../includes/sidebar.php'", "require_once __DIR__ . '/../includes/sidebar.php'")
            nc = nc.replace("include '../includes/header.php'", "include __DIR__ . '/../includes/header.php'")
            nc = nc.replace("include '../includes/footer.php'", "include __DIR__ . '/../includes/footer.php'")
            nc = nc.replace("include '../database.php'", "include __DIR__ . '/../database.php'")
            nc = nc.replace("require_once '../database.php'", "require_once __DIR__ . '/../database.php'")
            nc = nc.replace("require '../database.php'", "require __DIR__ . '/../database.php'")
            nc = nc.replace("include '../includes/", "include __DIR__ . '/../includes/")
            nc = nc.replace("require_once '../includes/", "require_once __DIR__ . '/../includes/")
            nc = nc.replace("require_once '../models/", "require_once __DIR__ . '/../models/")
            nc = nc.replace("require_once '../controllers/", "require_once __DIR__ . '/../controllers/")

            if nc != content:
                with open(fp, 'w', encoding='utf-8') as fh:
                    fh.write(nc)
                fixed.append(fp)
                print(f"Fixed: {fp}")
        except Exception as e:
            print(f"Error {fp}: {e}")

print(f"\nTotal: {len(fixed)} files fixed")
print("Now: git add . && git commit -m 'fix relative include paths' && git push")
