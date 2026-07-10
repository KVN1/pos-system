import os

# Simpler approach - form actions should POST back to the same page
# which then lets the controller handle it

fixed = []
skip = {'.git', 'backups', 'backup', 'vendor'}

replacements = [
    # Product forms - POST to /products which handles both GET and POST
    ('action="/controllers/ProductController.php"', 'action="/products"'),
    ('action="controllers/ProductController.php"', 'action="/products"'),
    # Category forms
    ('action="/controllers/CategoryController.php"', 'action="/categories"'),
    ('action="controllers/CategoryController.php"', 'action="/categories"'),
    # Sales forms
    ('action="/controllers/SalesController.php"', 'action="/add-sales-page"'),
    ('action="controllers/SalesController.php"', 'action="/add-sales-page"'),
    # Settings
    ('action="/controllers/SystemSettingsController.php"', 'action="/settings"'),
    ('action="controllers/SystemSettingsController.php"', 'action="/settings"'),
    # User forms
    ('action="/controllers/UserController.php"', 'action="/user/handle"'),
    ('action="controllers/UserController.php"', 'action="/user/handle"'),
    # Direct php files
    ('action="return_item.php"', 'action="/return-item"'),
    ('action="return_sale.php"', 'action="/return-sale"'),
    ('action="forgotpass.php"', 'action="/user/forgotpass"'),
    ('action="add-expense.php"', 'action="/expenses/add"'),
    ('action="/index.php?url=discounts"', 'action="/discounts"'),
]

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
            for old, new in replacements:
                nc = nc.replace(old, new)
            if nc != content:
                with open(fp, 'w', encoding='utf-8') as fh:
                    fh.write(nc)
                fixed.append(fp)
                print(f"Fixed: {fp}")
        except Exception as e:
            print(f"Error {fp}: {e}")

print(f"\nTotal: {len(fixed)} files fixed")
print("Now: git add . && git commit -m 'fix all form actions' && git push")
