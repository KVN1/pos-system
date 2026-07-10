import os

# Run from D:\xampp\htdocs\POSu
# Fixes relative paths like ../database.php to use absolute paths

fixed = []
skip = {'.git', 'backups', 'backup', 'vendor', 'node_modules'}

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

            # Fix relative db includes
            nc = nc.replace("require_once '../database.php'", "require_once __DIR__ . '/../database.php'")
            nc = nc.replace("require_once '../db.php'", "require_once __DIR__ . '/../db.php'")
            nc = nc.replace("include '../database.php'", "include __DIR__ . '/../database.php'")
            nc = nc.replace("require '../database.php'", "require __DIR__ . '/../database.php'")

            # Fix /views/ navigation links
            nc = nc.replace('/views/settings.php', '/settings')
            nc = nc.replace('/views/notifications.php', '/notifications')
            nc = nc.replace('/views/categories.php', '/categories')
            nc = nc.replace('/views/products.php', '/products')
            nc = nc.replace('/views/dashboard.php', '/dashboard')
            nc = nc.replace('/views/sales.php', '/sales')
            nc = nc.replace('/views/expenses.php', '/expenses')
            nc = nc.replace('/views/Activity.php', '/activity')
            nc = nc.replace('/views/usermanual.php', '/usermanual')
            nc = nc.replace('/views/admin.php', '/admin')
            nc = nc.replace('/views/add-sales.php', '/add-sales-page')
            nc = nc.replace('/views/return_item.php', '/return-item')
            nc = nc.replace('/views/return_sale.php', '/return-sale')
            nc = nc.replace('/views/search-expired.php', '/search-expired')
            nc = nc.replace('/views/change_password.php', '/change-password')

            if nc != content:
                with open(fp, 'w', encoding='utf-8') as fh:
                    fh.write(nc)
                fixed.append(fp)
                print(f"Fixed: {fp}")
        except Exception as e:
            print(f"Error: {fp}: {e}")

print(f"\nTotal: {len(fixed)} files fixed")
print("Now run: git add . && git commit -m 'fix all relative and view paths' && git push")
